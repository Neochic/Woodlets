/* globals ajaxurl, document */
requirejs([
    'jquery',
    'modal',
    'jquery-ui.sortable',
    'field-type-media',
    'field-type-rte'
], function ($, modal) {
    $.noConflict(true);

    var editor = $('.neochic-woodlets-editor');
    if (editor) {
        $('body').addClass('neochic-woodlets-editor-active');
    }

    $('.neochic-woodlets-col > ul').sortable({
        connectWith: '.neochic-woodlets-col > ul',
        placeholder: "neochic-woodlets-placeholder",
        update: updateData,
        receive: function (e, ui) {
            var widgetId = ui.item.data('widget');
            var allowed = $(this).parent().data('allowed');
            if ($.inArray(widgetId, allowed) < 0) {
                ui.sender.sortable("cancel");
            }
        }
    });

    function updateData() {
        var data = {};
        $('.neochic-woodlets-col').each(function () {
            var col = [];
            $(this).find('> ul > li:not(.no-elements)').each(function () {
                col.push({
                    "widgetId": $(this).data('widget'),
                    "instance": $(this).data('instance')
                });
            });
            data[$(this).data('id')] = col;
        });
        editor.children('input[name=neochic_woodlets_data]').val(JSON.stringify(data));
    }

    editor.on('click', '.add-element', function (e) {
        e.preventDefault();
        var col = $(this).parents('.neochic-woodlets-col').first();
        $.ajax({
            method: "post",
            url: ajaxurl,
            data: {
                action: "neochic_woodlets_get_widget_list",
                allowed: $(this).parent().data('allowed')
            }
        }).done(function (data) {
            var content = $(data);
            content.on('click', '.widget-top', function () {
                var widget = $(this).parents('.widget').data('widget');

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
                    col.children('ul').append(item);
                    updateData();
                    item.trigger('click');
                });
            });
            modal.open(content);
        });
    });

    editor.on('click', '.neochic-woodlets-col li', function () {
        var widget = $(this).data('widget');
        var el = $(this);
        var data = {
            action: "neochic_woodlets_get_widget_form",
            widget: widget,
            instance: $(this).data('instance')
        };

        $.ajax({
            method: "post",
            url: ajaxurl,
            data: data
        }).done(function (data) {
            var form = $('<form>' + data + '<button type="submit" class="button button-primary">Save</button></form>');
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
                            "instance": instance
                        }
                    }).done(function (result) {
                        el.replaceWith(result);
                    });
                    modal.close();
                    updateData();
                });

                e.preventDefault();
            });

            modal.open(form);
            $(document).trigger('neochic-woodlets-form-init', form);
        });
    });

});