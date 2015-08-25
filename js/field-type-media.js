/*
 * Takes care of wp.media initialization and
 * preview of media element.
 */

define(['jquery'], function($) {
    $(document).on('neochic-woodlets-form-init', function (e, form) {
        var $form = $(form);

        function removePreview($input) {
            $input.siblings('.neochic-woodlets-preview').remove();
        }

        function createPreview($input) {
            removePreview($input);
            var data = $input.data('value');
            if(!data) {
                return;
            }
            var preview = $('<div/>', {
                class: 'neochic-woodlets-preview'
            });

            if(data.type === 'image') {
                preview.append($('<img />', {
                    src: data.url,
                    alt: data.alt
                }));
            } else {
                preview.append('<strong>'+data.title+'</strong>');
            }
            $input.siblings('label').after(preview);
        }

        $form.find('.neochic-woodlets-media input').each(function() {
            createPreview($(this));
        });

        $form.on('click', '.neochic-woodlets-upload, .neochic-woodlets-preview, .neochic-woodlets-media label', function() {
            event.preventDefault();

            var $input = $(this).siblings('input');
            var wpMedia = $input.data('wpMedia');

            if (wpMedia) {
                wpMedia.open();
                return;
            }

            wpMedia = wp.media($input.data('settings'));
            $input.data('wpMedia', wpMedia);

            wpMedia.on('select', function () {
                var attachment = wpMedia.state().get('selection').first().toJSON();

                $input.val(attachment.id);
                $input.data('value', attachment);
                $input.parent().removeClass('neochic-woodlets-media-empty');

                createPreview($input);
            });

            wpMedia.open();
        });

        $form.on('click', '.neochic-woodlets-remove', function() {
            event.preventDefault();

            var $input = $(this).siblings('input');

            $input.val('');
            $input.parent().addClass('neochic-woodlets-media-empty');

            removePreview($input);
        });
    });
});