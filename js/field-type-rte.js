/*
 * Initializes Wordpress TinyMCE for
 * Rich-Text field type.
 */

/* globals tinymce, document */

define(['jquery', 'native-change'], function($, nativeChange) {
    function init(form) {
        $(form).find('.neochic-woodlets-rte').each(function () {
            var $input = $(this);
            var id = $input.attr('id');

            var settings = $.extend({},
                tinymce.settings,
                $(this).data('settings'),
                {
                    setup: function(editor) {
                        editor.on('input change keyup', function() {
                            $input.val(tinymce.get(id).getContent());
                            nativeChange($input.get(0));
                        });
                    }
                }
            );

            tinymce.execCommand('mceRemoveEditor', false, id);
            new tinymce.Editor(id, settings, tinymce.EditorManager).render();
        });
    }

    function end(form) {
        $(form).find('.neochic-woodlets-rte').each(function () {
            var id = $(this).attr('id');
            tinymce.execCommand('mceRemoveEditor', false, id);
        });
    }

    $(document).on('neochic-woodlets-form-init', function (e, form) {
        init(form);
    });

    $(document).on('neochic-woodlets-modal-unstack', function (e, form) {
        init(form);
    });

    $(document).on('neochic-woodlets-form-end', function(e, form) {
        end(form);
    });

    $(document).on('neochic-woodlets-modal-close', function(e, form) {
        end(form);
    });

    $(document).on('neochic-woodlets-modal-stack', function(e, form) {
        end(form);
    });
});
