$(document).ready(function () {
    // Ignore non-workqeue pages.
    if (!$('#workqueue_consents').length) {
        return;
    }

    var checkFilters = function () {
        if ($('#filters select[name=activityStatus]').val() == 'withdrawn') {
            $('#filters select').not('[name=activityStatus], [name=organization]').val('');
            $('#filters select').not('[name=activityStatus], [name=organization]').prop('disabled', true);
        } else {
            $('#filters select').prop('disabled', false);
        }
    };

    var showColumns = function () {
        // Get the columns API object
        var columns = workQueueTable.columns();

        // Toggle the visibility
        columns.visible(true);
    };

    var collapseFilters = function () {
        $('#participant_lookup_group input[type=text]').each(function () {
            if (this.value) {
                $('#participant_lookup_group_label').trigger('click');
                return false;
            }
        });
        $('#filter_status_group input[type=radio]:checked').each(function () {
            if (this.value) {
                $('#filter_status_group_label').trigger('click');
                return false;
            }
        });
        $('#filter_status_group input[type=text]').each(function () {
            if (this.value) {
                $('#filter_status_group_label').trigger('click');
                return false;
            }
        });
        $('.filter-status-sub-group input[type=radio]:checked').each(function () {
            if (this.value) {
                var groupId = $(this).closest('.filter-status-sub-group').attr('id');
                $('#' + groupId + '_label').trigger('click');
            }
        });
        $('.filter-status-sub-group input[type=text]').each(function () {
            if (this.value) {
                var groupId = $(this).closest('.filter-status-sub-group').attr('id');
                $('#' + groupId + '_label').trigger('click');
            }
        });
    };

    checkFilters();
    collapseFilters();
    $('#filters select, #filters input[type=radio]').on('change', function () {
        checkFilters();
        $('#filters').submit();
    });


    $('#filters #participant_search').on('click', function () {
        if ($('input[name=lastName]').val() && $('input[name=dateOfBirth]').val()) {
            $('input[name=participantId]').val('');
            checkFilters();
            $('#filters').submit();
        }
    });

    $('#filters #participant_id_search').on('click', function () {
        if ($('input[name=participantId]').val()) {
            $('input[name=lastName], input[name=dateOfBirth]').val('');
            checkFilters();
            $('#filters').submit();
        }
    });

    $('#filters .apply-date-filter').on('click', function () {
        var dateFieldName = $(this).data('consent-date-field-name');
        if ($('input[name=' + dateFieldName + 'StartDate]').val() !== '' || $('input[name=' + dateFieldName + 'EndDate]').val() !== '') {
            checkFilters();
            $('#filters').submit();
        }
    });

    $('#columns_reset').on('click', function () {
        $('#columns_group input[type=checkbox]').prop('checked', true);
        showColumns();
    });

    $('#participant_lookup_reset').on('click', function () {
        $('#participant_lookup_group input[type=text]').val('');
        checkFilters();
        $('#filters').submit();
    });

    $('#filter_status_reset').on('click', function () {
        $('#filter_status_group input[type=text]').val('');
        $('#filter_status_group input[type=radio][value=""]').prop('checked', true);
        checkFilters();
        $('#filters').submit();
    });

    $('.filter-status-sub-group-reset').on('click', function () {
        var groupId = $(this).data('group-id');
        $('#' + groupId + ' input[type=text]').val('');
        $('#' + groupId + ' input[type=radio][value=""]').prop('checked', true);
        checkFilters();
        $('#filters').submit();
    });

    var exportLimit = $('#workqueue_consents').data('export-limit');

    var workQueueExportWarningModel = function (location) {
        var exportLimitFormatted = exportLimit;
        if (window.Intl && typeof window.Intl === 'object') {
            exportLimitFormatted = new Intl.NumberFormat().format(exportLimit);
        }
        new PmiConfirmModal({
            title: 'Warning',
            msg: 'Note that the export reaches the limit of ' + exportLimitFormatted + ' participants. If your intent was to capture all participants, you may need to apply filters to ensure each export is less than ' + exportLimitFormatted + ' or utilize the Ops Data API. Please contact <em>sysadmin@pmi-ops.org</em> for more information.',
            isHTML: true,
            onTrue: function () {
                window.location = location;
            },
            btnTextTrue: 'Ok'
        });
    };

    $('button.export').on('click', function () {
        var location = $(this).data('href');
        var count = parseInt($('.count').html());
        new PmiConfirmModal({
            title: 'Attention',
            msg: 'The file you are about to download contains information that is sensitive and confidential. By clicking "accept" you agree not to distribute either the file or its contents, and to adhere to the <em>All of Us</em> Privacy and Trust Principles. A record of your acceptance will be stored at the Data and Research Center.',
            isHTML: true,
            onTrue: function () {
                if (count > exportLimit) {
                    workQueueExportWarningModel(location);
                } else {
                    window.location = location;
                }
            },
            btnTextTrue: 'Accept'
        });
    });

    var url = window.location.href;

    var columnsDef = $('#workqueue_consents').data('columns-def');

    var tableColumns = [];

    var generateTableRow = function (field, columnDef) {
        var row = {};
        row.name = field;
        row.data = field;
        if (columnDef.hasOwnProperty('htmlClass')) {
            row.class = columnDef['htmlClass'];
        }
        if (columnDef.hasOwnProperty('orderable')) {
            row.class = columnDef['orderable'];
        }
        tableColumns.push(row);
    };

    for (const [field, columnDef] of Object.entries(columnsDef)) {
        if (columnDef.hasOwnProperty('displayNames')) {
            Object.keys(columnDef['displayNames']).forEach(function (key, _i) {
                generateTableRow(key + 'Consent', columnDef);
            });
        } else {
            generateTableRow(field, columnDef);
        }
    }

    var workQueueTable = $('#workqueue_consents').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: {
            url: url,
            type: "POST"
        },
        order: [[5, 'desc']],
        dom: 'lrtip',
        columns: tableColumns,
        pageLength: 25,
        createdRow: function (row, data) {
            if (data.isWithdrawn === true) {
                $(row).addClass('tr-withdrawn');
            }
        }
    });

    // Populate count in header
    $('#workqueue_consents').on('init.dt', function (e, settings, json) {
        var count = json.recordsFiltered;
        $('#heading-count .count').text(count);
        if (count == 1) {
            $('#heading-count .plural').hide();
        } else {
            $('#heading-count .plural').show();
        }
        $('#heading-count').show();
    });

    $('#workqueue_length').addClass('pull-right');
    $('#workqueue_info').addClass('pull-left');

    // Display custom error message
    $.fn.dataTable.ext.errMode = 'none';
    $('#workqueue_consents').on('error.dt', function (e) {
        alert('An error occurred please reload the page and try again');
    });

    // Scroll to top when performing pagination
    $('#workqueue_consents').on('page.dt', function () {
        //Took reference from https://stackoverflow.com/a/21627503
        $('html').animate({
            scrollTop: $('#filters').offset().top
        }, 'slow');
        $('thead tr th:first-child').trigger('focus').trigger('blur');
    });

    $('.toggle-vis').on('click', function () {
        var column = workQueueTable.column($(this).attr('data-column'));
        column.visible(!column.visible());
        var columnName = $(this).attr('name');
        // Set column names in session
        $.get("/s/workqueue/consent/columns", {columnName: columnName, checked: $(this).prop('checked')});
    });

    var toggleColumns = function () {
        $('#columns_group input[type=checkbox]').each(function () {
            var column = workQueueTable.column($(this).attr('data-column'));
            column.visible($(this).prop('checked'));
        });
    };

    toggleColumns();
});
