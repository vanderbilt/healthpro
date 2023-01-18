<?php

namespace App\Service;

use App\Audit\Log;
use App\Entity\Measurement;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;

class HFHRepairService
{
    private EntityManagerInterface $em;
    private LoggerService $loggerService;
    private $env;

    public function __construct(EntityManagerInterface $em, LoggerService $logger, EnvironmentService $env)
    {
        $this->em = $em;
        $this->loggerService = $logger;
        $this->env = $env;
    }

    public function repairHFHParticipants(int $repairLimit = 100, string $csvFile = "src/Cache/HFSitePairing.csv"): void
    {
        $this->em->getConnection()->beginTransaction();
        $fhandle = fopen($csvFile, 'r');
        $headers = fgetcsv($fhandle);
        $count = 0;
        while ($row = fgetcsv($fhandle)) {
            $count++;
            try {
                $this->repairParticipantSite($row[0], $row[3], $row[4]);
            } catch (\Exception $exception) {
                $this->em->getConnection()->rollBack();
                $this->loggerService->log(Log::PROBLEM_NOTIFIY, $exception->getMessage());
                return;
            }
            if ($count === $repairLimit) {
                $this->em->flush();
                $this->em->clear();
                $this->em->getConnection()->commit();
                fclose($fhandle);
                $CSVArray = file_get_contents($csvFile);
                $CSVArray = explode("\r\n", $CSVArray);
                $CSVArray = array_slice($CSVArray, 1 + $count);
                $CSVArray = array_merge([implode(',', $headers)], $CSVArray);
                file_put_contents($csvFile, implode("\r\n", $CSVArray));
                break;
            }
        }
    }

    private function repairParticipantSite(string $participantId, string $currentSite, string $repairSite): void
    {
        $repairSite = strtolower($repairSite);
        $repairSite = str_replace('hpo-site-', '', $repairSite);
        $currentSite = strtolower($currentSite);
        $currentSite = str_replace('hpo-site-', '', $currentSite);
        $evaluation = $this->em->getRepository(Measurement::class)->findBy(['participantId' => $participantId, 'finalizedSite' => $currentSite]);
        $orders = $this->em->getRepository(Order::class)->findBy(['participantId' => $participantId, 'finalizedSite' => $currentSite]);
        if (count($orders) == 0) {
            throw new \Exception("No order found for participant $participantId at site $currentSite");
        }
        foreach ($orders as $order) {
            $this->loggerService->log(Log::ORDER_EDIT, $order->getId());
            $order->setFinalizedSite($repairSite);
        }
        foreach ($evaluation as $measurement) {
            $this->loggerService->log(Log::EVALUATION_EDIT, $measurement->getId());
            $measurement->setFinalizedSite($repairSite);
        }
    }
}