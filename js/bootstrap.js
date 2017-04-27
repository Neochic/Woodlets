/* globals document, window, ajaxurl, MutationObserver */

requirejs([
    'jquery',
    'content-area-manager',
    'field-type-media',
    'field-type-rte',
    'field-type-datepicker',
    'field-type-location',
    'field-type-slider',
    'debounce',
    'field-type-content-area',
    'page-config-inheritance',
    'init-external-scripts'
], function ($, contentAreaManager) {
    $(document).ready(function() {
        /*
         * init main content editor
         */
        $('#woodlets-fake-editor').detach();

        var $editor = $('.neochic-woodlets-editor');
        var $input = $editor.children('input[name=neochic_woodlets_data]');

        if (!$editor.length) {
            $('body').addClass('neochic-woodlets-editor-inactive');
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

            if (MutationObserver) {
                var observer = new MutationObserver(function (mutations) {
                    for (var i in mutations) {
                        var mutation = mutations[i];
                        if(mutation.type !== 'attributes' || mutation.attributeName !== 'class') {
                            continue;
                        }

                        var $target = $(mutation.target);
                        if(!$target.is('.customize-pane-child')) {
                            continue;
                        }
                        if($target.is('.open.busy')) {
                            $(document).trigger('neochic-woodlets-form-init', $target);
                        } else if($target.is('.busy:not(.open)')) {
                            $(document).trigger('neochic-woodlets-form-end', $target);
                        }
                    }
                });

                observer.observe($cc.get(0), {
                    attributes: true,
                    attributeFilter: ['class'],
                    subtree: true
                });
            }
        }

        /*
         * init page config
         */

        var $pageSections = $("[id^='neochic-woodlets-page_section_']");
        $pageSections.each(function() {
          $(this).trigger('neochic-woodlets-form-init', $(this));
        });

        /*
         * init profile page
         */

        var $profileForm = $(".neochic-woodlets-profile-form");
        $(this).trigger('neochic-woodlets-form-init', $profileForm);
    });

    /*
     * init widgets for sidebars
     */

    /*
     * we need to use the WordPress jQuery to listen to WordPress events
     * therefor global jQuery is used instead of local $
     *
     * todo: we need to find a good and reliable solution for widget
     *       initialization
     */
    var initWidgets = function() {
        $('.widget:not(#available-widgets .widget)')
            .filter('[id*="neochic_woodlets"]')
            .find('.widget-content')
            .each(function () {
                if ($(this).contents().get(0).nodeValue !== ' woodlets initialized ') {
                    $(this).prepend('<!-- woodlets initialized -->');
                    $(this).trigger('neochic-woodlets-form-init', $(this));
                }
            });
    };

    $(document).ready(function() {
        if($('.widget-liquid-right').length) {
            initWidgets();
        }
    });

    if (window.jQuery) {
        window.jQuery(document).on('widget-added widget-updated', function() {
            initWidgets();
        });
    }

    /*
     * init dismission of notifications
     */

    $(document).on('click', '.neochic-woodlets-notice .notice-dismiss', function() {
        var key = $(this).closest('.neochic-woodlets-notice').data('key');
        var value = $(this).closest('.neochic-woodlets-notice').data('value') || true;
        if (!key) {
            return;
        }

        $.ajax({
            method: "post",
            url: ajaxurl,
            data: {
                action: "neochic_woodlets_dismiss_admin_notice",
                key: key,
                value: value
            }
        });
    });
});
