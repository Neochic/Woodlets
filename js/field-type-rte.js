/*
 * Initializes Wordpress TinyMCE for
 * Rich-Text field type.
 */

/* globals tinymce, document */

define(['jquery'], function($) {
    function init(form) {
        $(form).find('.neochic-woodlets-rte').each(function () {
            var id = $(this).attr('id');

            var settings = $.extend({}, tinymce.settings, $(this).data('settings'));

            tinymce.execCommand('mceRemoveEditor', false, id);
            new tinymce.Editor(id, settings, tinymce.EditorManager).render();
        });
    }

    $(document).on('neochic-woodlets-form-init', function (e, form) {
        init(form);
    });

    $(document).on('neochic-woodlets-form-end', function (e, form) {
        $(form).find('.neochic-woodlets-rte').each(function () {
            var id = $(this).attr('id');
            $(this).val(tinymce.get(id).getContent());
        });
    });

    $(document).on('neochic-woodlets-modal-unstack', function (e, form) {
        init(form);
    });
});
