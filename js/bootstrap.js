/* globals document */

requirejs([
    'jquery',
    'content-area-manager',
    'field-type-media',
    'field-type-rte',
    'field-type-content-area'
], function ($, contentAreaManager) {
    $.noConflict(true);

    var $editor = $('.neochic-woodlets-editor');
    var $input = $editor.children('input[name=neochic_woodlets_data]');

    if ($editor) {
        $('body').addClass('neochic-woodlets-editor-active');
    }

    contentAreaManager($('.neochic-woodlets-col'), function(data) {
        $input.val(JSON.stringify(data));
    });

    var $cc = $('#customize-controls');
    if($cc.length) {
        $(document).trigger('neochic-woodlets-form-init', $cc);
    }
});
