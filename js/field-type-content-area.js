/*
 * Handles content area field type.
 */

/* globals document */
define([
    'jquery',
    'content-area-manager',
    'native-change'
], function($, contentAreaManager, nativeChange) {

    $(document).on('neochic-woodlets-form-init', function (e, form) {
        var $areas = $(form).find('.woodlets-content-area');
        contentAreaManager($areas, function(data) {
            $areas.each(function(key, area) {
                var $area = $(area);
                var $input = $area.find("input");
                $input.val(JSON.stringify(data[$area.data("id")]));
                nativeChange($input.get(0));
            });
        });
    });
});
