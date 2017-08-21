<?php
namespace Pmi\Entities;

use Pmi\Util;
use Pmi\Drc\CodeBook;
use Pmi\Application\AbstractApplication as Application;

class Participant
{
    public $status = true;
    public $statusReason;
    public $id;
    protected $rdrData;

    public function __construct($rdrParticipant = null)
    {
        if (is_object($rdrParticipant)) {
            $this->rdrData = $rdrParticipant;
            $this->parseRdrParticipant($rdrParticipant);
        }
    }

    public function parseRdrParticipant($participant)
    {
        if (!is_object($participant)) {
            return;
        }

        // Use participant id as id
        if (isset($participant->participantId)) {
            $this->id = $participant->participantId;
        }

        // HealthPro status is active if participant is consented, has completed basics survey, and is not withdrawn
        if (empty($participant->questionnaireOnTheBasics) || $participant->questionnaireOnTheBasics !== 'SUBMITTED') {
            $this->status = false;
            $this->statusReason = 'basics';
        }
        if (empty($participant->consentForStudyEnrollment) || $participant->consentForStudyEnrollment !== 'SUBMITTED') {
            // RDR should not be returning participant data for unconsented participants, but adding this check to be safe
            $this->status = false;
            $this->statusReason = 'consent';
        }
        if (!empty($participant->withdrawalStatus) && $participant->withdrawalStatus === 'NO_USE') {
            $this->status = false;
            $this->statusReason = 'withdrawal';
        }

        // Check for participants associated with TEST organization in prod
        if (getenv('PMI_ENV') === Application::ENV_PROD && $participant->hpoId === 'TEST') {
            $this->status = false;
            $this->statusReason = 'test-participant';
        }

        // Map gender identity to gender options for MayoLINK.
        switch (isset($participant->genderIdentity) ? $participant->genderIdentity : null) {
            case 'GenderIdentity_Woman':
                $this->gender = 'F';
                break;
            case 'GenderIdentity_Man':
                $this->gender = 'M';
                break;
            default:
                $this->gender = 'U';
                break;
        }

        // Set dob to DateTime object
        if (isset($participant->dateOfBirth)) {
            try {
                $this->dob = new \DateTime($participant->dateOfBirth);
            } catch (\Exception $e) {
                $this->dob = null;
            }
        }
    }

    public function getShortId()
    {
        if (strlen($this->id) >= 36) {
            return strtoupper(Util::shortenUuid($this->id));
        } else {
            return $this->id;
        }
    }

    public function getMayolinkDob()
    {
        return new \DateTime('1933-03-03');
    }

    public function getAddress()
    {
        $address = '';
        if ($this->streetAddress) {
            $address .= $this->streetAddress;
            if ($this->city || $this->state || $this->zipCode) {
                $address .= ', ';
            }
        }
        if ($this->city) {
            $address .= $this->city;
            $address .= $this->state ? ', ' : ' ';
        }
        if ($this->state) {
            $address .= $this->state . ' ';
        }
        if ($this->zipCode) {
            $address .= $this->zipCode;
        }
        return trim($address);
    }

    public function getAge()
    {
        if (!$this->dob) {
            return null;
        } else {
            return $this->dob
                ->diff(new \DateTime())
                ->y;
        }
    }

    /**
     * Magic methods for RDR data
     */
    public function __get($key)
    {
        if (isset($this->rdrData->{$key})) {
            return CodeBook::display($this->rdrData->{$key});
        } else {
            if (strpos($key, 'num') === 0) {
                return 0;
            } else {
                return null;
            }
        }
    }

    public function __isset($key)
    {
        return true;
    }

    public function checkIdentifiers($notes)
    {
        $identifiers = [];
        $dob = $this->dob;
        if ($dob) {
            $identifiers['dob'] = [
                $dob->format('m/d/y'),
                $dob->format('m-d-y'),
                $dob->format('m.d.y'),
                $dob->format('m/d/Y'),
                $dob->format('m-d-Y'),
                $dob->format('m.d.Y'),
                $dob->format('d/m/y'),
                $dob->format('d-m-y'),
                $dob->format('d.m.y'),
                $dob->format('d/m/Y'),
                $dob->format('d-m-Y'),
                $dob->format('d.m.Y')
            ];
        }
        if ($this->email) {
            $identifiers['email'] = [$this->email];
        }

        // Detect dob and email
        foreach ($identifiers as $key => $identifier) {
            foreach ($identifier as $value) {
                if (stripos($notes, $value) !== false) {
                    return [$key, $value];
                }
            }
        }

        // Detect name
        if ($this->firstName && $this->lastName) {
            $fName = preg_quote($this->firstName, '/');
            $lName = preg_quote($this->lastName, '/');
            if (preg_match("/(?:\W|^)({$fName}\W*{$lName}|{$lName}\W*{$fName})(?:\W|$)/i", $notes, $matches)) {
                return ['name', $matches[1]];
            }
        }

        // Detect address
        if ($this->streetAddress) {
            $address = preg_split('/[\s]/', $this->streetAddress);
            $address = array_map(function($value){
                return preg_quote($value, '/');
            }, $address);
            $pattern = '/(?:\W|^)';
            $pattern .= join('\W*', $address);
            $pattern .= '(?:\W|$)/i';

            if (preg_match($pattern, $notes, $matches)) {
                return ['address', $matches[0]];
            }
        }

        // Detect phone number
        $phone = preg_replace('/\D/', '', $this->phoneNumber);
        if ($phone) {
            $identifiers['phone'] = [$phone];
            if (strlen($phone) === 10) {
                $num1 = preg_quote(substr($phone, 0, 3));
                $num2 = preg_quote(substr($phone, 3, 3));
                $num3 = preg_quote(substr($phone, 6));
            }
            if (preg_match("/(\W*{$num1}\W*{$num2}\W*{$num3})/i", $notes, $matches)) {
                return ['phone', $matches[1]];
            }
        }
        return false;
    }
}
