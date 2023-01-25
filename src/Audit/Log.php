<?php

namespace App\Audit;

class Log
{
    // Actions
    public const REQUEST = 'REQUEST';
    public const LOGIN_SUCCESS = 'LOGIN_SUCCESS';
    public const LOGIN_FAIL = 'LOGIN_FAIL';
    public const LOGOUT = 'LOGOUT';
    public const INVALID_IP = 'INVALID_IP';
    public const ORDER_CREATE = 'ORDER_CREATE';
    public const ORDER_EDIT = 'ORDER_EDIT';
    public const ORDER_HISTORY_CREATE = 'ORDER_HISTORY_CREATE';
    public const EVALUATION_CREATE = 'EVALUATION_CREATE';
    public const EVALUATION_EDIT = 'EVALUATION_EDIT';
    public const EVALUATION_DELETE = 'EVALUATION_DELETE';
    public const EVALUATION_HISTORY_CREATE = 'EVALUATION_HISTORY_CREATE';
    public const SITE_EDIT = 'SITE_EDIT';
    public const SITE_ADD = 'SITE_ADD';
    public const SITE_DELETE = 'SITE_DELETE';
    public const NPH_SITE_EDIT = 'NPH_SITE_EDIT';
    public const NPH_SITE_ADD = 'NPH_SITE_ADD';
    public const NPH_SITE_DELETE = 'NPH_SITE_DELETE';
    public const WORKQUEUE_EXPORT = 'WORKQUEUE_EXPORT';
    public const CROSS_ORG_PARTICIPANT_ATTEMPT = 'CROSS_ORG_PARTICIPANT_ATTEMPT';
    public const CROSS_ORG_PARTICIPANT_AGREE = 'CROSS_ORG_PARTICIPANT_AGREE';
    public const CROSS_ORG_PARTICIPANT_VIEW = 'CROSS_ORG_PARTICIPANT_VIEW';
    public const WITHDRAWAL_NOTIFY = 'WITHDRAWAL_NOTIFY';
    public const DEACTIVATE_NOTIFY = 'DEACTIVATE_NOTIFY';
    public const DECEASED_PENDING_NOTIFY = 'DECEASED_PENDING_NOTIFY';
    public const DECEASED_APPROVED_NOTIFY = 'DECEASED_APPROVED_NOTIFY';
    public const EHR_WITHDRAWAL_NOTIFY = 'EHR_WITHDRAWAL_NOTIFY';
    public const PROBLEM_CREATE = 'PROBLEM_CREATE';
    public const PROBLEM_EDIT = 'PROBLEM_EDIT';
    public const PROBLEM_COMMENT_CREATE = 'PROBLEM_COMMENT_CREATE';
    public const PROBLEM_NOTIFIY = 'PROBLEM_NOTIFIY';
    public const QUEUE_RESEND_EVALUATION = 'QUEUE_RESEND_EVALUATION';
    public const MISSING_MEASUREMENTS_ORDERS_NOTIFY = 'MISSING_MEASUREMENTS_ORDERS_NOTIFY';
    public const NOTICE_EDIT = 'NOTICE_EDIT';
    public const NOTICE_ADD = 'NOTICE_ADD';
    public const NOTICE_DELETE = 'NOTICE_DELETE';
    public const PATIENT_STATUS_ADD = 'PATIENT_STATUS_ADD';
    public const PATIENT_STATUS_EDIT = 'PATIENT_STATUS_EDIT';
    public const PATIENT_STATUS_HISTORY_ADD = 'PATIENT_STATUS_HISTORY_ADD';
    public const PATIENT_STATUS_HISTORY_EDIT = 'PATIENT_STATUS_HISTORY_EDIT';
    public const AWARDEE_ADD = 'AWARDEE_ADD';
    public const ORGANIZATION_ADD = 'ORGANIZATION_ADD';
    public const BIOBANK_ORDER_FINALIZE_NOTIFY = 'BIOBANK_ORDER_FINALIZE_NOTIFY';
    public const PATIENT_STATUS_IMPORT_ADD = 'PATIENT_STATUS_IMPORT_ADD';
    public const PATIENT_STATUS_IMPORT_EDIT = 'PATIENT_STATUS_IMPORT_EDIT';
    public const GROUP_MEMBER_ADD = 'GROUP_MEMBER_ADD';
    public const GROUP_MEMBER_REMOVE = 'GROUP_MEMBER_REMOVE';
    public const GROUP_MEMBER_REMOVE_NOTIFY = 'GROUP_MEMBER_REMOVE_NOTIFY';
    public const CSRF_TOKEN_MISMATCH = 'CSRF_TOKEN_MISMATCH';
    public const INCENTIVE_ADD = 'INCENTIVE_ADD';
    public const INCENTIVE_EDIT = 'INCENTIVE_EDIT';
    public const INCENTIVE_REMOVE = 'INCENTIVE_REMOVE';
    public const ID_VERIFICATION_ADD = 'ID_VERIFICATION_ADD';
    public const INCENTIVE_IMPORT_ADD = 'INCENTIVE_IMPORT_ADD';
    public const INCENTIVE_IMPORT_EDIT = 'INCENTIVE_IMPORT_EDIT';
    public const ID_VERIFICATION_IMPORT_ADD = 'ID_VERIFICATION_IMPORT_ADD';
    public const ID_VERIFICATION_IMPORT_EDIT = 'ID_VERIFICATION_IMPORT_EDIT';
    public const FEATURE_NOTIFICATION_EDIT = 'FEATURE_NOTIFICATION_EDIT';
    public const FEATURE_NOTIFICATION_ADD = 'FEATURE_NOTIFICATION_ADD';
    public const FEATURE_NOTIFICATION_DELETE = 'FEATURE_NOTIFICATION_DELETE';
    public const NPH_ORDER_CREATE = 'NPH_ORDER_CREATE';
    public const NPH_ORDER_UPDATE = 'NPH_ORDER_UPDATE';
    public const NPH_SAMPLE_CREATE = 'NPH_SAMPLE_CREATE';
    public const NPH_SAMPLE_UPDATE = 'NPH_SAMPLE_UPDATE';
    public const NPH_ALIQUOT_CREATE = 'NPH_ALIQUOT_CREATE';
}
