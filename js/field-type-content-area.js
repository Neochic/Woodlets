/*
 * Handles content area field type.
 */

/* globals document */
define([
    'jquery',
    'content-area-manager'
], function($, contentAreaManager) {

    $(document).on('neochic-woodlets-form-init', function (e, form) {
        var $areas = $(form).find('.woodlets-content-area');
        contentAreaManager($areas, function(data) {
            $areas.each(function(key, area) {
                var $area = $(area);
                $area.find("input").val(JSON.stringify(data[$area.data("id")]));
            });
        });
    });
});
