<?php
namespace Pmi\WorkQueue;

class WorkQueue
{
    const LIMIT_DEFAULT = 1000;
    const LIMIT_EXPORT = 10000;
    const LIMIT_EXPORT_PAGE_SIZE = 1000;

    const HTML_SUCCESS = '<i class="fa fa-check text-success" aria-hidden="true"></i>';
    const HTML_DANGER = '<i class="fa fa-times text-danger" aria-hidden="true"></i>';

    protected $app;

    public static $wQColumns = [
        'lastName',
        'firstName',
        'dateOfBirth',
        'participantId',
        'biobankId',
        'language',
        'enrollmentStatus',
        'consentForStudyEnrollmentTime',
        'consentForElectronicHealthRecordsTime',
        'consentForCABoRTime',
        'withdrawalTime',
        'recontactMethod',
        'streetAddress',
        'email',
        'phoneNumber',
        'numCompletedBaselinePPIModules',
        'numCompletedPPIModules',
        'questionnaireOnTheBasics',
        'questionnaireOnTheBasicsTime',
        'questionnaireOnOverallHealth',
        'questionnaireOnOverallHealthTime',
        'questionnaireOnLifestyle',
        'questionnaireOnLifestyleTime',
        'questionnaireOnMedicalHistory',
        'questionnaireOnMedicalHistoryTime',
        'questionnaireOnMedications',
        'questionnaireOnMedicationsTime',
        'questionnaireOnFamilyHealth',
        'questionnaireOnFamilyHealthTime',
        'questionnaireOnHealthcareAccess',
        'questionnaireOnHealthcareAccessTime',
        'site',
        'organization',
        'physicalMeasurementsTime',
        'physicalMeasurementsFinalizedSite',
        'samplesToIsolateDNA',
        'numBaselineSamplesArrived',
        'sampleStatus1SST8',
        'sampleStatus1SST8Time',
        'sampleStatus1PST8',
        'sampleStatus1PST8Time',
        'sampleStatus1HEP4',
        'sampleStatus1HEP4Time',
        'sampleStatus1ED04',
        'sampleStatus1ED04Time',
        'sampleStatus1ED10',
        'sampleStatus1ED10Time',
        'sampleStatus2ED10',
        'sampleStatus2ED10Time',
        'sampleStatus1UR10',
        'sampleStatus1UR10Time',
        'sampleStatus1SAL',
        'sampleStatus1SALTime',
        'biospecimenSourceSite',
        'dateOfBirth',
        'sex',
        'genderIdentity',
        'race',
        'education',
    ];

    public static $filters = [
        'withdrawalStatus' => [
            'label' => 'Withdrawal Status',
            'options' => [
                'Withdrawn' => 'NO_USE',
                'Not withdrawn' => 'NOT_WITHDRAWN'
            ]
        ],
        'enrollmentStatus' => [
            'label' => 'Participant Status',
            'options' => [
                'Registered' => 'INTERESTED',
                'Member' => 'MEMBER',
                'Full Participant' => 'FULL_PARTICIPANT'
            ]
        ],
        'consentForElectronicHealthRecords' => [
            'label' => 'EHR Consent Status',
            'options' => [
                'Consented' => 'SUBMITTED',
                'Refused consent' => 'SUBMITTED_NO_CONSENT',
                'Consent not completed' => 'UNSET'
            ]
        ],
        'ageRange' => [
            'label' => 'Age',
            'options' => [
                '0-17' => '0-17',
                '18-25' => '18-25',
                '26-35' => '26-35',
                '36-45' => '36-45',
                '46-55' => '46-55',
                '56-65' => '56-65',
                '66-75' => '66-75',
                '76-85' => '76-85',
                '86+' => '86-'
            ]
        ],
        'genderIdentity' => [
            'label' => 'Gender Identity',
            'options' => [
                'Man' => 'GenderIdentity_Man',
                'Woman' => 'GenderIdentity_Woman',
                'Non-binary' => 'GenderIdentity_NonBinary',
                'Transgender' => 'GenderIdentity_Transgender',
                'Other' => 'GenderIdentity_AdditionalOptions'
            ]
        ],
        'race' => [
            'label' => 'Race',
            'options' => [
                'American Indian / Alaska Native' => 'AMERICAN_INDIAN_OR_ALASKA_NATIVE',
                'Black or African American' => 'BLACK_OR_AFRICAN_AMERICAN',
                'Asian' => 'ASIAN',
                'Native Hawaiian or Other Pacific Islander' => 'NATIVE_HAWAIIAN_OR_OTHER_PACIFIC_ISLANDER',
                'White' => 'WHITE',
                'Hispanic, Latino, or Spanish' => 'HISPANIC_LATINO_OR_SPANISH',
                'Middle Eastern or North African' => 'MIDDLE_EASTERN_OR_NORTH_AFRICAN',
                'H/L/S and White' => 'HLS_AND_WHITE',
                'H/L/S and Black' => 'HLS_AND_BLACK',
                'H/L/S and one other race' => 'HLS_AND_ONE_OTHER_RACE',
                'H/L/S and more than one other race' => 'HLS_AND_MORE_THAN_ONE_OTHER_RACE',
                'More than one race' => 'MORE_THAN_ONE_RACE',
                'Other' => 'OTHER_RACE'
            ]
        ]
    ];

    // These are currently not working in the RDR
    public static $filtersDisabled = [
        'language' => [
            'label' => 'Language',
            'options' => [
                'English' => 'SpokenWrittenLanguage_English',
                'Spanish' => 'SpokenWrittenLanguage_Spanish'
            ]
        ],
        'recontactMethod' => [
            'label' => 'Contact Method',
            'options' => [
                'House Phone' => 'RecontactMethod_HousePhone',
                'Cell Phone' => 'RecontactMethod_CellPhone',
                'Email' => 'RecontactMethod_Email',
                'Physical Address' => 'RecontactMethod_Address'
            ]
        ],
        'sex' => [
            'label' => 'Sex',
            'options' => [
                'Male' => 'SexAtBirth_Male',
                'Female' => 'SexAtBirth_Female',
                'Intersex' => 'SexAtBirth_Intersex'
            ]
        ],
        'sexualOrientation' => [
            'label' => 'Sexual Orientation',
            'options' => [
                'Straight' => 'SexualOrientation_Straight',
                'Gay' => 'SexualOrientation_Gay',
                'Lesbian' => 'SexualOrientation_Lesbian',
                'Bisexual' => 'SexualOrientation_Bisexual',
                'Other' => 'SexualOrientation_None'
            ]
        ],
        // ne not supported with enums
        'race' => [
            'label' => 'Race',
            'options' => [
                'White' => 'WHITE',
                'Not white' => 'neWHITE'
            ]
        ]
    ];

    public static $surveys = [
        'TheBasics' => 'Basics',
        'OverallHealth' => 'Health',
        'Lifestyle' => 'Lifestyle',
        'MedicalHistory' => 'Hist',
        'Medications' => 'Meds',
        'FamilyHealth' => 'Family',
        'HealthcareAccess' => 'Access'
    ];

    public static $samples = [
        '1SST8' => '8 mL SST',
        '1PST8' => '8 mL PST',
        '1HEP4' => '4 mL Na-Hep',
        '1ED04' => '4 mL EDTA',
        '1ED10' => '1st 10 mL EDTA',
        '2ED10' => '2nd 10 mL EDTA',
        '1UR10' => 'Urine 10 mL',
        '1SAL' => 'Saliva'
    ];

    public static $samplesAlias = [
        [
            '1SST8' => '1SS08',
            '1PST8' => '1PS08'
        ],
        [
            '1SST8' => '2SST8',
            '1PST8' => '2PST8'
        ]
    ];

    public function generateTableRows($participants, $app)
    {
        $e = function($string) {
            return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };
        $this->app = $app;
        $rows = [];
        foreach ($participants as $participant) {
            $row = [];
            //Identifiers and status
            if($app->hasRole('ROLE_USER')) {
                $row['lastName'] = $this->generateLink($participant->id, $participant->lastName);
            } else {
                $row['lastName'] = $e($participant->lastName);
            }
            if ($app->hasRole('ROLE_USER')) {
                $row['firstName'] = $this->generateLink($participant->id, $participant->firstName);
            } else {
                $row['firstName'] = $e($participant->firstName);
            }
            if ($participant->dob) {
                $row['dateOfBirth'] = $participant->dob->format('m/d/Y'); 
            } else {
                $row['dateOfBirth'] = '';
            }
            $row['participantId'] = $e($participant->id);
            $row['biobankId'] = $e($participant->biobankId);
            $row['language'] = $e($participant->language);
            $row['participantStatus'] = $e($participant->enrollmentStatus);
            $row['generalConsent'] = $this->displayStatus($participant->consentForStudyEnrollment, 'SUBMITTED', $participant->consentForStudyEnrollmentTime);
            $row['ehrConsent'] = $this->displayStatus($participant->consentForElectronicHealthRecords, 'SUBMITTED', $participant->consentForElectronicHealthRecordsTime);
            $row['caborConsent'] = $this->displayStatus($participant->consentForCABoR, 'SUBMITTED', $participant->consentForCABoRTime);
            if ($participant->withdrawalStatus == 'NO_USE') {
                $row['withdrawal'] = self::HTML_DANGER . '<span class="text-danger">No Use</span> - ' . self::dateFromString($participant->withdrawalTime, $app->getUserTimezone());
            } else {
                $row['withdrawal'] = ''; 
            }

            //Contact
            $row['contactMethod'] = $e($participant->recontactMethod);
            if ($participant->getAddress()) {
                $row['address'] = $e($participant->getAddress());
            } else {
                $row['address'] = '';  
            }
            $row['email'] = $e($participant->email);
            $row['phone'] = $e($participant->phoneNumber);

            //PPI Surveys
            if ($participant->numCompletedBaselinePPIModules == 3) {
                $row['ppiStatus'] = self::HTML_SUCCESS;
            }
            else {
                $row['ppiStatus'] = self::HTML_DANGER;
            }
            $row['ppiSurveys'] = $e($participant->numCompletedPPIModules);
            foreach (self::$surveys as $field => $survey) {
                $row["ppi{$field}"] = $this->displayStatus($participant->{'questionnaireOn' . $field}, 'SUBMITTED');
                if (!empty($participant->{'questionnaireOn' . $field . 'Time'})) {
                    $row["ppi{$field}Time"] = self::dateFromString($participant->{'questionnaireOn' . $field . 'Time'}, $app->getUserTimezone());
                } else {
                    $row["ppi{$field}Time"] = '';
                }
            }

            //In-Person Enrollment
            $row['pairedSite'] = $e($participant->siteSuffix);
            $row['pairedOrganization'] = $e($participant->organization);
            $row['physicalMeasurementsStatus'] = $this->displayStatus($participant->physicalMeasurementsStatus, 'COMPLETED', $participant->physicalMeasurementsTime);
            $row['evaluationFinalizedSite'] = $e($participant->evaluationFinalizedSite);
            $row['biobankDnaStatus'] = $this->displayStatus($participant->samplesToIsolateDNA, 'RECEIVED');
            if ($participant->numBaselineSamplesArrived >= 7) {
                $row['biobankSamples'] = self::HTML_SUCCESS . $e($participant->numBaselineSamplesArrived);
            } else {
                $row['biobankSamples'] = '';
            }
            foreach (array_keys(self::$samples) as $sample) {
                $newSample = $sample;
                if (array_key_exists($sample, self::$samplesAlias[0]) && $participant->{"sampleStatus" . self::$samplesAlias[0][$sample]} == 'RECEIVED') {
                    $newSample = self::$samplesAlias[0][$sample];
                } elseif (array_key_exists($sample, self::$samplesAlias[1]) && $participant->{"sampleStatus" . self::$samplesAlias[1][$sample]} == 'RECEIVED') {
                    $newSample = self::$samplesAlias[1][$sample];
                }
                $row["sample{$sample}"] = $this->displayStatus($participant->{'sampleStatus' . $newSample}, 'RECEIVED');
                if (!empty($participant->{'sampleStatus' . $newSample . 'Time'})) {
                    $row["sample{$sample}Time"] = self::dateFromString($participant->{'sampleStatus' . $newSample . 'Time'}, $app->getUserTimezone());
                } else {
                    $row["sample{$sample}Time"] = '';
                }
            }
            $row['orderCreatedSite'] = $e($participant->orderCreatedSite);

            //Demographics
            $row['age'] = $e($participant->age);
            $row['sex'] = $e($participant->sex);
            $row['genderIdentity'] = $e($participant->genderIdentity);
            $row['race'] = $e($participant->race);
            $row['education'] = $e($participant->education);
            array_push($rows, $row);
        } 
        return $rows;
    }

    public static function dateFromString($string, $timezone)
    {
        if (!empty($string)) {
            try {
                $date = new \DateTime($string);
                $date->setTimezone(new \DateTimeZone($timezone));
                return $date->format('m/d/Y');
            } catch (\Exception $e) {
                return '';
            }
        } else {
            return '';
        }
    }

    public static function csvDateFromObject($date)
    {
        return is_object($date) ? $date->format('m/d/Y') : '';
    }

    public static function csvStatusFromSubmitted($status)
    {
        return $status === 'SUBMITTED' ? 1 : 0;
    }

    public function displayStatus($value, $successStatus, $time = null)
    {
        if ($value === $successStatus) {
            if ($time) {
                return self::HTML_SUCCESS . ' ' . self::dateFromString($time, $this->app->getUserTimezone());
            }
            return self::HTML_SUCCESS;
        } else {
            return self::HTML_DANGER;
        }
    }

    public function generateLink($id, $name)
    {
        return '<a href="/participant/' . urlencode($id) . '">' . htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';;
    }
}
