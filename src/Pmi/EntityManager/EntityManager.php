<?php
namespace Pmi\EntityManager;

use Pmi\Util;

class EntityManager
{
    protected $dbal;

    protected $entities = [
        'users' => 'doctrine',
        'orders' => 'doctrine',
        'evaluations' => 'doctrine',
        'sites' => 'doctrine',
        'withdrawal_log' => 'doctrine',
        'problems' => 'doctrine',
        'problem_comments' => 'doctrine',
        'evaluations_queue' => 'doctrine',
        'organizations' => 'doctrine',
        'awardees' => 'doctrine',
        'missing_notifications_log' => 'doctrine',
        'evaluations_history' => 'doctrine',
        'orders_history' => 'doctrine',
        'notices' => 'doctrine',
        'patient_status' => 'doctrine',
        'patient_status_history' => 'doctrine'
    ];

    protected $timezone;

    public function setDbal($dbal)
    {
        $this->dbal = $dbal;
    }

    public function getRepository($entity) {
        if (!array_key_exists($entity, $this->entities)) {
            throw new \Exception('Entity not defined');
        }
        switch ($this->entities[$entity]) {
            case 'doctrine':
                if (!$this->dbal) {
                    throw new \Exception('No DBAL available');
                }
                return new DoctrineRepository($this->dbal, $entity, $this->getTimezone());

            default:
                throw new \Exception('Invalid entity type');
        }
    }

    public function fetchAll($query, $parameters)
    {
        $result = $this->dbal->fetchAll($query, $parameters);
        return Util::parseMultipleTimestamps($result, $this->timezone);
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }
}
