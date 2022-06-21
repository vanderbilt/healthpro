<?php

namespace App\Controller;

use App\Entity\PatientStatus;
use App\Repository\PatientStatusRepository;
use App\Service\OnSiteDetailsReportingService;
use App\Service\SiteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/on-site")
 */
class OnSiteDetailsReportingController extends BaseController
{
    public function __construct(
        EntityManagerInterface $em
    )
    {
        parent::__construct($em);
    }

    /**
     * @Route("/patient-status", name="on_site_patient_status")
     */
    public function index(OnSiteDetailsReportingService $onSiteDetailsReportingService, PatientStatusRepository $patientStatusRepository, SiteService $siteService, Request $request)
    {
        //For ajax requests
        if ($request->isXmlHttpRequest()) {
            $params = $request->request->all();
            $patientStatuses = $patientStatusRepository->getOnsitePatientStatuses($params['length'], $params['start'], $siteService->getSiteOrganization());
            $ajaxData = [];
            $ajaxData['data'] = $onSiteDetailsReportingService->getAjaxData($patientStatuses);
            $ajaxData['recordsTotal'] = $ajaxData['recordsFiltered'] = $patientStatusRepository->getOnsitePatientStatusesCount();
            return $this->json($ajaxData);
        } else {
            return $this->render('onsite/patient-status.html.twig');
        }
    }
}
