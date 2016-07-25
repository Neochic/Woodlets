/*
 * Initializes a jquery-ui slider
 * For slider field-type.
 */

/* globals document */

define([
    "jquery",
    "jquery-ui.slider"
], function($) {

    function init(form) {

        form = $(form);

        form.find('.neochic-woodlets-slider-input:not(.initialized)').each(function(i, sliderContainer) {
            sliderContainer = $(sliderContainer);
            sliderContainer.addClass('initialized');

            var input = sliderContainer.find("input");
            var slider = sliderContainer.find(".slider");
            var display = sliderContainer.find(".display");

            slider.slider({
                value: input.val(),
                min: sliderContainer.data("min"),
                max: sliderContainer.data("max"),
                step: sliderContainer.data("step"),
                slide: function( event, ui ) {
                    input.val(ui.value);
                    display.text(ui.value);
                }
            });

        });
    }

    $(document).on('neochic-woodlets-form-init', function (e, form) {
        init(form);
    });

    $(document).on('neochic-woodlets-modal-unstack', function (e, form) {
        init(form);
    });
});
