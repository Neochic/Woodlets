/*
 * Initializes jQuery UI Datepicker for
 * Datepicker field type.
 */

/* globals document */

define(['jquery', 'native-change', 'jquery-ui.datepicker'], function($, nativeChange) {
    function init(form) {
        var checkForNativeSupport = document.createElement("input");
        checkForNativeSupport.setAttribute("type", "date");
        if(checkForNativeSupport.type !== "text") {
            return;
        }

        $('body').addClass('neochic-woodlets-date-fallback');

        $(form).find('.neochic-woodlets-date').each(function() {
            var $dateInput = $(this);
            var $input = $(this).next();
            var removeClassTimeout = null;
            $input.datepicker({
                altField: $(this),
                altFormat: "yy-mm-dd",
                beforeShow: function() {
                    if (removeClassTimeout) {
                        window.clearTimeout(removeClassTimeout);
                    }
                    $('#ui-datepicker-div').addClass("neochic-woodlets-datepicker");
                },
                onClose: function() {
                    removeClassTimeout = window.setTimeout(function() {
                        $('#ui-datepicker-div').removeClass("neochic-woodlets-datepicker");
                    }, 1000);
                },
                onSelect: function() {
                    nativeChange($dateInput.get(0));
                }
            });

            if ($(this).val()) {
                $input.datepicker( "setDate", new Date($(this).val()));
            }
        });
    }

    $(document).on('neochic-woodlets-form-init', function (e, form) {
        init(form);
    });

    $(document).on('neochic-woodlets-modal-unstack', function (e, form) {
        init(form);
    });
});
