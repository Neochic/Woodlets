/* globals document, ajaxurl, tinymce */
define([
    'jquery',
    'modal',
    'jquery-ui.sortable'
], function ($, modal) {
    return function(areas, callback) {
        var $areas = $(areas).filter(':not(.initialized)');

        $areas.addClass('initialized');

        var updateData = function() {
            if(typeof tinymce !== 'undefined' && tinymce.get('content')) {
                //use WordPress native beforeunload warning if content is modified
                tinymce.get('content').isNotDirty = false;
            }
            var data = {};
            $areas.each(function () {
                var col = [];
                $(this).find('> ul, > div > ul').find('> li[data-widget]:not(.no-elements)').each(function () {
                    col.push({
                        "widgetId": $(this).data('widget'),
                        "instance": $(this).data('instance')
                    });
                });
                data[$(this).data('id')] = col;
            });

            callback(data);
        };

        $areas.find('> ul, > div > ul').sortable({
            connectWith: $areas.find('> ul, > div > ul'),
            placeholder: "neochic-woodlets-placeholder",
            delay: 250,
            update: updateData,
            receive: function (e, ui) {
                var $area = $(this).closest('.woodlets-content-area');
                var widgetId = ui.item.data('widget');
                var allowed = $area.data('allowed');
                if ($.inArray(widgetId, allowed) < 0) {
                    ui.sender.sortable("cancel");
                }
            }
        });

        $areas.on('click', '.add-element', function (e) {
            var $button = $(this);
            if ($button.hasClass('blocked')) {
                return;
            }
            $button.addClass('blocked');

            e.preventDefault();
            var $area = $(this).closest('.woodlets-content-area');
            $.ajax({
                method: "post",
                url: ajaxurl,
                data: {
                    action: "neochic_woodlets_get_widget_list",
                    allowed: $area.data('allowed')
                }
            }).done(function (data) {
                var $content = $(data);
                var selectWidget = function($widget, preventClose) {
                    var widget = $widget.closest('.widget').data('widget');

                    $.ajax({
                        method: "post",
                        url: ajaxurl,
                        data: {
                            "action": "neochic_woodlets_get_widget_preview",
                            "widget": widget,
                            "instance": null
                        }
                    }).done(function (result) {
                        var item = $(result);
                        $area.find('> ul, > div > ul').append(item);
                        updateData();
                        if (!preventClose) {
                            modal.close();
                        }
                        item.trigger('click');
                    });
                };

                var $widgets = $content.find('.widget-top');
                if ($widgets.length === 1) {
                    selectWidget($widgets, true);
                    return;
                }

                $content.on('click', '.widget-top', function () {
                    selectWidget($(this));
                });

                $content.on('click', '.cancel', function() {
                    modal.close();
                });

                modal.open($content, 'Add item');
                $button.removeClass('blocked');
            });
        });

        $areas.on('click', '.delete', function (e) {
            $(e.target).closest('.neochic-woodlets-widget').remove();
            updateData();
        });

        $areas.on('click', '> ul > li.neochic-woodlets-widget, > div > ul > li.neochic-woodlets-widget', function (e) {
            var el = $(this);
            if (el.hasClass('blocked')) {
                return;
            }

            el.addClass('blocked');

            /*
             * stop propagation to prevent widget getting collapsed by WordPress
             */
            e.stopPropagation();

            /*
             * do not open widget form if an action (like "delete") is clicked
             */
            if (!$(e.target).is('.edit') && $(e.target).closest('.row-actions').length > 0) {
                return;
            }

            var widget = $(this).data('widget');

            //this should be done more elegant!
            var name = $(this).find(".widget-title h4").text();

            var data = {
                action: "neochic_woodlets_get_widget_form",
                widget: widget,
                instance: JSON.stringify(el.data('instance'))
            };

            $.ajax({
                method: "post",
                url: ajaxurl,
                data: data
            }).done(function (data) {
                var form = $('<form>' + data + '<span class="button cancel">Cancel</span> <button type="submit" class="button button-primary">Save</button></form>');
                form.on('submit', function (e) {
                    $(document).trigger('neochic-woodlets-form-end', form);
                    $.ajax({
                        method: "post",
                        url: ajaxurl,
                        data: $(this).serialize() + '&widget=' + widget + '&action=neochic_woodlets_get_widget_update'
                    }).done(function (result) {
                        var instance = $.parseJSON(result);

                        el.data('instance', instance);

                        $.ajax({
                            method: "post",
                            url: ajaxurl,
                            data: {
                                "action": "neochic_woodlets_get_widget_preview",
                                "widget": widget,
                                "instance": JSON.stringify(instance)
                            }
                        }).done(function (result) {
                            el.replaceWith($(result));
                        });
                        modal.close();
                        updateData();
                    });

                    e.preventDefault();
                });

                form.on('click', '.cancel', function() {
                    modal.close();
                });

                modal.open(form, name);
                $(document).trigger('neochic-woodlets-form-init', form);
                el.removeClass('blocked');
            });
        });
    };
});
