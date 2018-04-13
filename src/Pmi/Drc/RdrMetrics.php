<?php
namespace Pmi\Drc;

class RdrMetrics
{
    protected $rdrHelper;

    public function __construct(RdrHelper $rdrHelper)
    {
        $this->rdrHelper = $rdrHelper;
    }

    public function metrics($start_date, $end_date)
    {
        $client = $this->rdrHelper->getClient();
        $response = $client->request('POST', 'rdr/v1/Metrics', [
            'json' => [
                'start_date' => $start_date,
                'end_date' => $end_date
            ]
        ]);
        $responseObject = json_decode($response->getBody()->getContents(), True);
        return $responseObject;
    }

    public function participantCountsOverTime($start_date, $end_date, $stratification, $enrollmentStatus, $awardee)
    {
        $client = $this->rdrHelper->getClient();
        $queryString =
            '?startDate=' . $start_date .
            '&endDate=' . $end_date .
            '&stratification=' . $stratification .
            '&enrollmentStatus=' . $enrollmentStatus .
            '&awardee' . $awardee;

        $response = $client->request('GET', 'rdr/v1/ParticipantCountsOverTime' . $queryString);
        $responseObject = json_decode($response->getBody()->getContents(), True);
        return $responseObject;
    }

    public function metricsFields()
    {
        $client = $this->rdrHelper->getClient();
        $response = $client->request('GET', 'rdr/v1/MetricsFields');
        $responseObject = json_decode($response->getBody()->getContents(), True);
        return $responseObject;
    }
}
