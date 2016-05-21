/* globals document */

requirejs([
    'jquery',
    'content-area-manager',
    'field-type-media',
    'field-type-rte',
    'field-type-content-area',
    'page-config-inheritance'
], function ($, contentAreaManager) {
    $.noConflict(true);

    /*
     * init main content editor
     */
    var $editor = $('.neochic-woodlets-editor');
    var $input = $editor.children('input[name=neochic_woodlets_data]');

    if ($editor) {
        $('body').addClass('neochic-woodlets-editor-active');
    }

    contentAreaManager($('.neochic-woodlets-col'), function(data) {
        $input.val(JSON.stringify(data));
    });

    /*
     * init customizer
     */
    var $cc = $('#customize-controls');
    if($cc.length) {
        $(document).trigger('neochic-woodlets-form-init', $cc);
    }

    /*
     * init page config
     */

    var $pageSections = $("[id^='neochic-woodlets-page_section_']");
    $pageSections.each(function() {
      $(this).trigger('neochic-woodlets-form-init', $(this));
    });
});
