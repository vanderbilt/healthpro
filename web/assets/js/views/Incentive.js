$(document).ready(function () {
    $("#incentive form").parsley();

    $('#incentive_incentive_date_given').pmiDateTimePicker({
        format: 'MM/DD/YYYY',
        maxDate: new Date().setHours(23, 59, 59, 999),
        useCurrent: false
    });

    var incentivePrefix = 'incentive_';

    var handleIncentiveFormFields = function (that, idPrefix = '') {
        var selectFieldId = $(that).attr('id').replace(incentivePrefix, '');
        var otherFieldSelector = idPrefix + '#' + incentivePrefix + 'other_' + selectFieldId;
        if ($(that).val() === 'other') {
            $(otherFieldSelector).parent().show();
            $(otherFieldSelector).attr('required', 'required');
        } else {
            $(otherFieldSelector).parent().hide();
            $(otherFieldSelector).val('');
            $(otherFieldSelector).removeAttr('required');
        }
        if (selectFieldId === 'incentive_type') {
            var giftCardFieldSelector = idPrefix + '#' + incentivePrefix + 'gift_card_type';
            if ($(that).val() === 'gift_card') {
                $(giftCardFieldSelector).parent().show();
                $(giftCardFieldSelector).attr('required', 'required');
            } else {
                $(giftCardFieldSelector).parent().hide();
                $(giftCardFieldSelector).val('');
                $(giftCardFieldSelector).removeAttr('required');
            }
        }
    };

    var showHideIncentiveFormFields = function (idPrefix = '') {
        var incentiveFormSelect = $(idPrefix + '#incentive select');

        incentiveFormSelect.each(function () {
            handleIncentiveFormFields(this, idPrefix);
        });

        incentiveFormSelect.change(function () {
            handleIncentiveFormFields(this, idPrefix);
        });
    };

    showHideIncentiveFormFields();

    if ($('.incentive-form').find('div').hasClass('alert-danger')) {
        $('[href="#on_site_details"]').tab('show');
    }

    var incentivePanelCollapse = $('#incentive .panel-collapse');

    incentivePanelCollapse.on('show.bs.collapse', function () {
        $(this).siblings('.panel-heading').addClass('active');
    });

    incentivePanelCollapse.on('hide.bs.collapse', function () {
        $(this).siblings('.panel-heading').removeClass('active');
    });

    $('#incentive_cancel').on('click', function () {
       $('.incentive-form')[0].reset();
       showHideIncentiveFormFields();
    });

    $(".incentive-amend").on('click', function () {
        var url = $(this).data('href');
        $('#incentive_amend_ok').data('href', url);
        $('#incentive_amend_modal').modal('show');
    });

    $(".incentive-remove").on('click', function () {
        var incentiveId = $(this).data('id');
        $('#incentive_remove_id').val(incentiveId);
        $('#incentive_remove_modal').modal('show');
    });

    $("#incentive_amend_ok").on('click', function () {
        var incentiveEditFormModal = $('#incentive_edit_form_modal');
        var modelContent = $("#incentive_edit_form_modal .modal-content");
        modelContent.html('');
        // Load data from url
        modelContent.load($(this).data('href'), function () {
            incentiveEditFormModal.modal('show');
        });
    });

    $('#incentive_edit_form_modal').on('shown.bs.modal', function () {
        showHideIncentiveFormFields('#incentive_edit_form_modal ');
        $("#incentive_edit_form_modal form").parsley();
    });
});
