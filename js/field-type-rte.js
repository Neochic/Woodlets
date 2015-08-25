/*
 * Initializes Wordpress TinyMCE for
 * Rich-Text field type.
 */

define(['jquery'], function($) {
    $(document).on('neochic-woodlets-form-init', function (e, form) {
        $(form).find('.neochic-woodlets-rte').each(function () {
            var id = $(this).attr('id');

            var settings = $.extend({}, tinymce.settings, $(this).data('settings'));

            tinymce.execCommand('mceRemoveEditor', false, id);
            new tinymce.Editor(id, settings, tinymce.EditorManager).render();
        });
    });

    $(document).on('neochic-woodlets-form-end', function (e, form) {
        $(form).find('.neochic-woodlets-rte').each(function () {
            var id = $(this).attr('id');
            $(this).val(tinyMCE.get(id).getContent());
        });
    });
});