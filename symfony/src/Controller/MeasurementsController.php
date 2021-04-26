<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Entity\Site;
use App\Entity\User;
use App\Form\MeasurementType;
use App\Service\EnvironmentService;
use App\Service\LoggerService;
use App\Service\MeasurementService;
use App\Service\OrderService;
use App\Service\ParticipantSummaryService;
use App\Service\SiteService;
use Doctrine\ORM\EntityManagerInterface;
use Pmi\Audit\Log;
use Pmi\Evaluation\Evaluation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * @Route("/s")
 */
class MeasurementsController extends AbstractController
{

    protected $em;
    protected $measurementService;
    protected $participantSummaryService;
    protected $loggerService;
    protected $siteService;

    public function __construct(
        EntityManagerInterface $em,
        MeasurementService $measurementService,
        ParticipantSummaryService $participantSummaryService,
        LoggerService $loggerService,
        SiteService $siteService
    ) {
        $this->em = $em;
        $this->measurementService = $measurementService;
        $this->participantSummaryService = $participantSummaryService;
        $this->loggerService = $loggerService;
        $this->siteService = $siteService;
    }

    /**
     * @Route("/participant/{participantId}/measurement/{measurementId}", name="measurement", defaults={"measurementId": null})
     */
    public function measurementsAction($participantId, $measurementId, Request $request)
    {
        $participant = $this->participantSummaryService->getParticipantById($participantId);
        if (!$participant) {
            throw $this->createNotFoundException('Participant not found.');
        }
        $type = $request->query->get('type');
        if (!$this->measurementService->canEdit($measurementId,
                $participant) || $this->siteService->isTestSite() || ($participant->activityStatus === 'deactivated' && empty($measurementId))) {
            throw $this->createAccessDeniedException();
        }
        if ($measurementId) {
            $measurement = $this->em->getRepository(Measurement::class)->find($measurementId);
            if (!$measurement) {
                throw $this->createNotFoundException('Physical Measurement not found.');
            }
            $this->measurementService->load($measurement, $type);
            $measurement->canCancel = $measurement->canCancel();
            $measurement->canRestore = $measurement->canRestore();
            $measurement->reasonDisplayText = $measurement->getReasonDisplayText();
        } else {
            $measurement = null;
            if ($this->measurementService->isBloodDonorForm() && $request->query->get('wholeblood')) {
                $measurement->setData((object)[
                    'weight-protocol-modification' => 'whole-blood-donor'
                ]);
            }
        }
        $showAutoModification = false;

        $measurementsForm = $this->createForm(MeasurementType::class, null, [
            'schema' => $measurement->getSchema(),
            'locked' => $measurement->getFinalizedTs() ? true : false
        ]);
        $measurementsForm->handleRequest($request);
        if ($measurementsForm->isSubmitted()) {
            // Check if PMs are cancelled
            if ($this->measurementService->isEvaluationCancelled()) {
                throw $this->createAccessDeniedException();
            }
            // Check if finalized_ts is set and rdr_id is empty
            if (!$this->measurementService->isEvaluationFailedToReachRDR()) {
                if ($measurementsForm->isValid()) {
                    if ($this->measurementService->isBloodDonorForm()) {
                        $this->measurementService->addBloodDonorProtocolModificationForRemovedFields();
                        if ($request->request->has('finalize') && (!$measurement || empty($measurement->getRdrId()))) {
                            $this->measurementService->addBloodDonorProtocolModificationForBloodPressure();
                        }
                    }
                    $this->measurementService->setData($measurementsForm->getData());
                    $dbArray = $this->measurementService->toArray();
                    $now = new \DateTime();
                    $dbArray['updated_ts'] = $now;
                    if ($request->request->has('finalize') && (!$measurement || empty($measurement->getRdrId()))) {
                        $errors = $this->measurementService->getFinalizeErrors();
                        if (count($errors) === 0) {
                            $measurement->setFinalizedTs($now);
                            if (!$measurement) {
                                $measurement->setParticipantId($participant->id);
                                $measurement->setUserId($this->getUser()->getId());
                                $measurement->setSite($this->siteService->getSiteId());
                            }
                            $measurement->setFinalizedUserId($this->getUser()->getId());
                            $measurement->setFinalizedSite($this->siteService->getSiteId());
                            // Send final evaluation to RDR and store resulting id
                            if ($measurement != null && $measurement->getParentId() != null) {
                                $parentEvaluation = $this->em->getRepository(Evaluation::class)->findOneBy([
                                    'id' => $measurement->getParentId()
                                ]);
                                $fhir = $this->measurementService->getFhir($now, $parentEvaluation->getRdrId());
                            } else {
                                if (!$measurement) {
                                    $this->measurementService->loadFromArray($dbArray);
                                }
                                $fhir = $this->measurementService->getFhir($now);
                            }
                            if ($rdrEvalId = $this->measurementService->createEvaluation($participant->id, $fhir)) {
                                $measurement->setRdrId($rdrEvalId);
                                $measurement->setFhirVersion(\Pmi\Evaluation\Fhir::CURRENT_VERSION);
                            } else {
                                $this->addFlash('error', 'Failed to finalize the physical measurements. Please try again');
                                $rdrError = true;
                            }
                        } else {
                            foreach ($errors as $field) {
                                if (is_array($field)) {
                                    list($field, $replicate) = $field;
                                    $measurementsForm->get($field)->get($replicate)->addError(new FormError($this->measurementService->getFormFieldErrorMessage($field,
                                        $replicate)));
                                } else {
                                    $measurementsForm->get($field)->addError(new FormError($this->measurementService->getFormFieldErrorMessage($field)));
                                }
                            }
                            $measurementsForm->addError(new FormError('Physical measurements are incomplete and cannot be finalized. Please complete the missing values below or specify a protocol modification if applicable.'));
                            $showAutoModification = $this->measurementService->canAutoModify();
                        }
                    }
                    if (!$measurement || $request->request->has('copy')) {
                        $measurement->setUser($this->getUser()->getId());
                        $measurement->setSite($this->siteService->getSiteId());
                        $measurement->setParticipantId($participant->id);
                        $measurement->setCreatedTs($dbArray['updated_ts']);
                        if ($request->request->has('copy')) {
                            $measurement->setParentId($measurement->getId());
                            $measurement->setCreatedTs = $measurement->getCreatedTs();
                        }
                        $this->em->persist($measurement);
                        $this->em->flush();
                        $measurementId = $measurement->getId();
                        if ($measurementId) {
                            $this->loggerService->log(Log::EVALUATION_CREATE, $measurementId);
                            if (empty($rdrError)) {
                                $this->addFlash('notice',
                                    !$request->request->has('copy') ? 'Physical measurements saved' : 'Physical measurements copied');
                            }

                            // If finalization failed, new physical measurements are created, but
                            // show errors and auto-modification options on subsequent display
                            if (!$measurementsForm->isValid()) {
                                return $this->redirectToRoute('evaluation', [
                                    'participantId' => $participant->id,
                                    'evalId' => $measurementId,
                                    'showAutoModification' => 1
                                ]);
                            } else {
                                return $this->redirectToRoute('evaluation', [
                                    'participantId' => $participant->id,
                                    'evalId' => $measurementId
                                ]);
                            }
                        } else {
                            $this->addFlash('error', 'Failed to create new physical measurements');
                        }
                    } else {
                        $this->em->persist($measurement);
                        $this->em->flush();
                        $this->loggerService->log(Log::EVALUATION_EDIT, $measurementId);
                        if (empty($rdrError)) {
                            $this->addFlash('notice', 'Physical measurements saved');
                        }

                        // If finalization failed, values are still saved, but do not redirect
                        // so that errors can be displayed
                        if ($measurementsForm->isValid()) {
                            return $this->redirectToRoute('evaluation', [
                                'participantId' => $participant->id,
                                'evalId' => $measurementId
                            ]);
                        }
                    }
                } elseif (count($measurementsForm->getErrors()) == 0) {
                    $measurementsForm->addError(new FormError('Please correct the errors below'));
                }
            } else {
                // Send measurements to RDR
                if ($this->measurementService->sendToRdr()) {
                    $this->addFlash('success', 'Physical measurements finalized');
                } else {
                    $this->addFlash('error', 'Failed to finalize the physical measurements. Please try again');
                }
                return $this->redirectToRoute('evaluation', [
                    'participantId' => $participant->id,
                    'measurementId' => $measurementId
                ]);
            }
        } elseif ($request->query->get('showAutoModification')) {
            // if new physical measurements were created and failed to finalize, generate errors post-redirect
            $errors = $this->measurementService->getFinalizeErrors();
            if (count($errors) > 0) {
                foreach ($errors as $field) {
                    if (is_array($field)) {
                        list($field, $replicate) = $field;
                        $measurementsForm->get($field)->get($replicate)->addError(new FormError($this->measurementService->getFormFieldErrorMessage($field,
                            $replicate)));
                    } else {
                        $measurementsForm->get($field)->addError(new FormError($this->measurementService->getFormFieldErrorMessage($field)));
                    }
                }
                $measurementsForm->addError(new FormError('Physical measurements are incomplete and cannot be finalized. Please complete the missing values below or specify a protocol modification if applicable.'));
                $showAutoModification = $this->measurementService->canAutoModify();
            }
        }

        return $app['twig']->render('evaluation.html.twig', [
            'participant' => $participant,
            'measurement' => $measurement,
            'measurementForm' => $measurementsForm->createView(),
            'schema' => $this->measurementService->getAssociativeSchema(),
            'warnings' => $this->measurementService->getWarnings(),
            'conversions' => $this->measurementService->getConversions(),
            'latestVersion' => $this->measurementService->getLatestFormVersion(),
            'showAutoModification' => $showAutoModification,
            'revertForm' => $this->measurementService->getEvaluationRevertForm()->createView(),
            'displayEhrBannerMessage' => $this->measurementService->requireEhrModificationProtocol() || $this->measurementService->isEhrProtocolForm(),
            'ehrProtocolBannerMessage' => $app->getConfig('ehr_protocol_banner_message')
        ]);
    }
}