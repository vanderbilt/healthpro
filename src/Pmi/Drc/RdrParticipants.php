<?php
namespace Pmi\Drc;

use Pmi\Entities\Participant;
use Ramsey\Uuid\Uuid;

class RdrParticipants
{
    protected $rdrHelper;
    protected $client;
    protected $cacheEnabled = true;
    protected static $resourceEndpoint = 'rdr/v1/';
    protected $nextToken;
    protected $total;

    public function __construct(RdrHelper $rdrHelper)
    {
        $this->rdrHelper = $rdrHelper;
        $this->cacheEnabled = $rdrHelper->isCacheEnabled();
        $this->cacheTime = $rdrHelper->getCacheTime();
    }

    protected function getClient()
    {
        if (!is_object($this->client)) {
            $this->client = $this->rdrHelper->getClient(self::$resourceEndpoint);
        }
        return $this->client;
    }

    protected function participantToResult($participant)
    {
        return new Participant($participant);
    }

    protected function paramsToQuery($params)
    {
        $query = [];
        if (isset($params['lastName'])) {
            $query['lastName'] = ucfirst($params['lastName']);
        }
        if (isset($params['firstName'])) {
            $query['firstName'] = ucfirst($params['firstName']);
        }
        if (isset($params['dob'])) {
            try {
                $date = new \DateTime($params['dob']);
                $query['dateOfBirth'] = $date->format('Y-m-d');
            } catch (\Exception $e) {
                throw new Exception\InvalidDobException();
            }
            if (strpos($params['dob'], $date->format('Y')) === false) {
                throw new Exception\InvalidDobException('Please enter a four digit year');
            } elseif ($date > new \DateTime('today')) {
                throw new Exception\InvalidDobException('Date of birth cannot be a future date');
            }
        }

        return $query;
    }

    public function search($params)
    {
        $query = $this->paramsToQuery($params);
        try {
            $response = $this->getClient()->request('GET', 'ParticipantSummary', [
                'query' => $query
            ]);
        } catch (\Exception $e) {
            $this->rdrHelper->logException($e);
            throw new Exception\FailedRequestException();
        }
        $responseObject = json_decode($response->getBody()->getContents());

        if (!is_object($responseObject)) {
            throw new Exception\InvalidResponseException();
        }
        if (!isset($responseObject->entry) || !is_array($responseObject->entry)) {
            return [];
        }
        $results = [];
        foreach ($responseObject->entry as $participant) {
            if (isset($participant->resource) && is_object($participant->resource)) {
                if ($result = $this->participantToResult($participant->resource)) {
                    $results[] = $result;
                }
            }
        }

        return $results;
    }

    /**
     * @param string|array $params Particpant Summary API parameters (query string or array)
     * @param bool $next Enable paging
     **/
    public function listParticipantSummaries($params, $next = false)
    {
        if ($next) {
            //Pass token if exists
            if ($this->nextToken) {
                if (is_array($params)) {
                    $params['_token'] = $this->nextToken;
                } else {
                    $params .= '&_token=' . $this->nextToken;
                }
            }
            // Request count
            if (is_array($params)) {
                $params['_includeTotal'] = 'true';
            } else {
                $params .= '&_includeTotal=true';
            }

        }
        $this->nextToken = $this->total = null;
        try {
            $response = $this->getClient()->request('GET', 'ParticipantSummary', [
                'query' => $params
            ]);
        } catch (\Exception $e) {
            $this->rdrHelper->logException($e);
            throw new Exception\FailedRequestException();
        }
        $contents = $response->getBody()->getContents();
        $responseObject = json_decode($contents);
        if (!is_object($responseObject)) {
            throw new Exception\InvalidResponseException();
        }
        if (!isset($responseObject->entry) || !is_array($responseObject->entry)) {
            return [];
        }
        if (isset($responseObject->link) && is_array($responseObject->link)) {
            foreach ($responseObject->link as $link) {
                if ($link->relation === 'next') {
                    $queryString = parse_url($link->url, PHP_URL_QUERY);
                    parse_str($queryString, $nextParameters);
                    if (isset($nextParameters['_token'])) {
                        $this->nextToken = $nextParameters['_token'];
                    }
                    break;
                }
            }
        }
        if (isset($responseObject->total)) {
            $this->total = intval($responseObject->total);
        }
        return $responseObject->entry;
    }

    public function getNextToken()
    {
        return $this->nextToken;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setNextToken($app, $tableParams, $type = null)
    {
        $tokens = $app['session']->get('tokens');
        $index = $tableParams['start'] + $tableParams['count'];
        if (empty($type) && !empty($tokens)) {
            $this->nextToken = $tokens[$tableParams['start']];
        } else {
            $tokens[$index] = $this->nextToken;
            $app['session']->set('tokens', $tokens);
        }
    }

    public function getById($id, $refresh = null)
    {
        if ($this->cacheEnabled) {
            $memcache = new \Memcache();
            $memcacheKey = 'rdr_participant_' . $id;
            $participant = $refresh != 1 ? $memcache->get($memcacheKey) : null;
        }
        if (!$this->cacheEnabled || !$participant) {
            try {
                $response = $this->getClient()->request('GET', "Participant/{$id}/Summary");
                $participant = json_decode($response->getBody()->getContents());
                if ($this->cacheEnabled) {
                    $participant->cacheTime = new \DateTime();
                    $memcache->set($memcacheKey, $participant, 0, $this->cacheTime);
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                return false;
            }
        }
        return $this->participantToResult($participant);
    }

    public function createParticipant($participant)
    {
        if (isset($participant['date_of_birth'])) {
            $dt = new \DateTime($participant['date_of_birth']);
            $participant['date_of_birth'] = $dt->format('Y-m-d');
        }
        try {
            $response = $this->getClient()->request('POST', 'Participant', [
                'json' => $participant
            ]);
            $result = json_decode($response->getBody()->getContents());
            if (is_object($result) && (isset($result->drc_internal_id) || isset($result->participant_id))) {
                return isset($result->drc_internal_id) ? $result->drc_internal_id : $result->participant_id;
            }
        } catch (\Exception $e) {
            $this->rdrHelper->logException($e);
            return false;
        }
        return false;
    }

    public function getEvaluation($participantId, $evaluationId)
    {
        try {
            $response = $this->getClient()->request('GET', "Participant/{$participantId}/PhysicalMeasurements/{$evaluationId}");
            $result = json_decode($response->getBody()->getContents());
            if (is_object($result) && isset($result->id)) {
                return $result;
            }
        } catch (\Exception $e) {
            $this->rdrHelper->logException($e);
            return false;
        }
        return false;
    }

    public function createEvaluation($participantId, $evaluation)
    {
        try {
            $response = $this->getClient()->request('POST', "Participant/{$participantId}/PhysicalMeasurements", [
                'json' => $evaluation
            ]);
            $result = json_decode($response->getBody()->getContents());
            if (is_object($result) && isset($result->id)) {
                return $result->id;
            }
        } catch (\Exception $e) {
            $this->rdrHelper->logException($e);
            return false;
        }
        return false;
    }

    public function getOrder($participantId, $orderId)
    {
        try {
            $response = $this->getClient()->request('GET', "Participant/{$participantId}/BiobankOrder/{$orderId}");
            $result = json_decode($response->getBody()->getContents());
            if (is_object($result) && isset($result->id)) {
                return $result;
            }
        } catch (\Exception $e) {
            $this->rdrHelper->logException($e);
            return false;
        }
        return false;
    }

    public function createOrder($participantId, $order)
    {
        try {
            $response = $this->getClient()->request('POST', "Participant/{$participantId}/BiobankOrder", [
                'json' => $order
            ]);
            $result = json_decode($response->getBody()->getContents());
            if (is_object($result) && isset($result->id)) {
                return $result->id;
            }
        } catch (\Exception $e) {
            $this->rdrHelper->logException($e);
            return false;
        }
        return false;
    }

    public function createMockBiobankSamples($participantId)
    {
        try {
            $response = $this->getClient()->request('POST', "DataGen", [
                'json' => ['create_biobank_samples' => $participantId]
            ]);
            $result = json_decode($response->getBody()->getContents());
            if (is_object($result) && isset($result->num_samples)) {
                return $result->num_samples;
            }
        } catch (\Exception $e) {
            $this->rdrHelper->logException($e);
            return false;
        }
        return false;
    }

    public function getLastError()
    {
        return $this->rdrHelper->getLastError();
    }

    public function getCacheEnabled()
    {
        return $this->cacheEnabled;
    }
}
