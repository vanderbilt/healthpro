<?php

namespace App\Helper;

/**
 * Define magic properties to fix phpstan errors
 * @property string $email
 * @property string $lastName
 * @property string $firstName
 * @property string $phoneNumber
 */

class NphParticipant
{
    public $id;
    public $cacheTime;
    public $rdrData;
    public $dob;


    public function __construct(?\stdClass $rdrParticipant = null)
    {
        if (is_object($rdrParticipant)) {
            if (!empty($rdrParticipant->cacheTime)) {
                $this->cacheTime = $rdrParticipant->cacheTime;
                unset($rdrParticipant->cacheTime);
            }
            $this->rdrData = $rdrParticipant;
            $this->parseRdrParticipant($rdrParticipant);
        }
    }

    private function parseRdrParticipant(\stdClass $participant)
    {
        if (!is_object($participant)) {
            return;
        }
        // Use nph participant id as id
        if (isset($participant->participantNphId)) {
            $this->id = $participant->participantNphId;
        }
        // Set dob to DateTime object
        if (isset($participant->DOB)) {
            try {
                $this->dob = new \DateTime($participant->DOB);
            } catch (\Exception $e) {
                $this->dob = null;
            }
        }
    }

    /**
     * Magic methods for RDR data
     */
    public function __get(string $key)
    {
        if (isset($this->rdrData->{$key})) {
            return $this->rdrData->{$key};
        }
        return null;
    }

    public function __isset(string $key)
    {
        return true;
    }
}