<?php

namespace App\Controller;

use App\Entity\Organizations;
use App\Entity\PatientStatus;
use App\Entity\PatientStatusHistory;
use App\Entity\PatientStatusImport;
use App\Entity\PatientStatusTemp;
use App\Service\CsvFileHandler;
use App\Service\LoggerService;
use App\Form\PatientStatusImportFormType;
use App\Form\PatientStatusImportConfirmFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/s")
 */
class PatientStatusController extends AbstractController
{
    /**
     * @Route("/patient/status/import", name="patientStatusImport", methods={"GET","POST"})
     */
    public function patientStatusImport(Request $request, SessionInterface $session, EntityManagerInterface $em, CsvFileHandler $csvFileHandler)
    {
        $form = $this->createForm(PatientStatusImportFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $file = $form['patient_status_csv']->getData();
            $fileName = $file->getClientOriginalName();
            $patientStatuses = [];
            $csvFileHandler->extractCsvFileData($file,$form, $patientStatuses);
            if ($form->isValid()) {
                if (!empty($patientStatuses)) {
                    $organization = $em->getRepository(Organizations::class)->findOneBy(['id' => $session->get('siteOrganizationId')]);
                    $patientStatusImport = new PatientStatusImport();
                    $patientStatusImport->setFileName($fileName);
                    $patientStatusImport->setOrganization($organization);
                    $patientStatusImport->setAwardee($session->get('siteAwardeeId'));
                    $patientStatusImport->setUserId($this->getUser()->getId());
                    $patientStatusImport->setSite($session->get('site')->id);
                    $patientStatusImport->setCreatedTs(new \DateTime());
                    $em->persist($patientStatusImport);
                    foreach ($patientStatuses as $key => $patientStatus) {
                        $patientStatusTemp = new PatientStatusTemp();
                        $patientStatusTemp->setParticipantId($patientStatus['participantId']);
                        $patientStatusTemp->setStatus($patientStatus['status']);
                        $patientStatusTemp->setComments($patientStatus['comments']);
                        $patientStatusTemp->setImport($patientStatusImport);
                        $em->persist($patientStatusTemp);
                    }
                    $em->flush();
                    $id = $patientStatusImport->getId();
                    $em->clear();
                    return $this->redirectToRoute('patientStatusImportConfirmation', ['id' => $id]);
                }
            } else {
                $form->addError(new FormError('Please correct the errors below'));
            }
        }
        $patientStatusImports = $em->getRepository(PatientStatusImport::class)->findBy(['user_id' => $this->getUser()->getId(), 'confirm' => 1], ['id' => 'DESC']);
        return $this->render('patientstatus/import.html.twig', [
            'importForm' => $form->createView(),
            'imports' => $patientStatusImports
        ]);
    }

    /**
     * @Route("/patient/status/confirmation/{id}", name="patientStatusImportConfirmation", methods={"GET", "POST"})
     */
    public function patientStatusImportConfirmation(int $id, Request $request, EntityManagerInterface $em)
    {
        $patientStatusImport = $em->getRepository(PatientStatusImport::class)->findOneBy(['id' => $id, 'user_id' => $this->getUser()->getId(), 'confirm' => 0]);
        if (empty($patientStatusImport)) {
            throw $this->createNotFoundException('Page Not Found!');
        }
        $form = $this->createForm(PatientStatusImportConfirmFormType::class);
        $form->handleRequest($request);
        $importPatientStatuses = $patientStatusImport->getPatientStatusTemps();
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($importPatientStatuses as $importPatientStatus) {
                $patientStatus = $em->getRepository(PatientStatus::class)->findOneBy([
                    'participant_id' => $importPatientStatus->getParticipantId(),
                    'organization' => $patientStatusImport->getOrganization()
                ]);
                if (!$patientStatus) {
                    $patientStatus = new PatientStatus();
                    $patientStatus->setParticipantId($importPatientStatus->getParticipantId());
                    $patientStatus->setAwardee($patientStatusImport->getAwardee());
                    $patientStatus->setOrganization($patientStatusImport->getOrganization());
                    $em->persist($patientStatus);
                }
                $patientStatusHistory = new PatientStatusHistory();
                $patientStatusHistory->setUserId($patientStatusImport->getUserId());
                $patientStatusHistory->setSite($patientStatusImport->getSite());
                $patientStatusHistory->setStatus($importPatientStatus->getStatus());
                $patientStatusHistory->setComments($importPatientStatus->getComments());
                $patientStatusHistory->setCreatedTs(new \DateTime());
                $patientStatusHistory->setPatientStatus($patientStatus);
                $patientStatusHistory->setImport($patientStatusImport);
                $em->persist($patientStatusHistory);
                $em->flush();
                $patientStatusHistoryId = $patientStatusHistory->getId();

                // Update history id in patient_status table
                $patientStatus = $em->getRepository(PatientStatus::class)->findOneBy([
                    'participant_id' => $importPatientStatus->getParticipantId(),
                    'organization' => $patientStatusImport->getOrganization()
                ]);
                $patientStatus->setHistoryId($patientStatusHistoryId);
                $em->persist($patientStatus);
                $em->flush();
            }
            // Update confirm status
            $patientStatusImport->setConfirm(1);
            $em->flush();
            $em->clear();
            $this->addFlash(
                'success',
                'Successfully Imported!'
            );
            return $this->redirectToRoute('patientStatusImport');
        }
        return $this->render('patientstatus/confirmation.html.twig', [
            'patientStatuses' => $importPatientStatuses,
            'importConfirmForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/patient/status/import/{id}", name="patientStatusImportDetails", methods={"GET"})
     */
    public function patientStatusImportDetails(int $id, Request $request, EntityManagerInterface $em)
    {
        $patientStatusImport = $em->getRepository(PatientStatusImport::class)->findOneBy(['id' => $id, 'user_id' => $this->getUser()->getId(), 'confirm' => 1]);
        if (empty($patientStatusImport)) {
            throw $this->createNotFoundException('Page Not Found!');
        }
        $patientStatusHistories = $patientStatusImport->getPatientStatusHistories();
        return $this->render('patientstatus/import-details.html.twig', [
            'patientStatusImport' => $patientStatusImport,
            'patientStatusHistories' => $patientStatusHistories
        ]);
    }
}
