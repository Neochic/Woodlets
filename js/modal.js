/*
 * Trivial modal to replace Thickbox from WordPress,
 * since it doesn't work as expected.
 */

define(['jquery'], function($) {
    var overlay = $('<div class="neochic-woodlets-modal" />');
    var contentFrame = null;
    var closeButton = $('<div class="tb-close-icon">Close</div>');
    var breadCrumbTrail = $('<ul class="woodlets-breadcrumb"></ul>');
    var $body = $('body');
    var stack = [];

    var clear = function() {
        closeButton.detach();
        breadCrumbTrail.detach();
        contentFrame.detach();
        overlay.detach();
        $body.removeClass('neochic-woodlets-modal-active');
    };

    var open = function(content, title) {
        if (contentFrame) {
            clear();
        }

        stack.push({
            content: content,
            title: title
        });

        var breadCrumbs = $();
        $.each(stack, function(index, value) {
            var breadCrumb = $('<li>'+ value.title +'</li>');
            breadCrumbs = breadCrumbs.add(breadCrumb);
            if (index !== stack.length - 1) {
                breadCrumb.attr('role', 'button');
            }
        });

        breadCrumbTrail.html('');
        breadCrumbTrail.append(breadCrumbs);

        contentFrame = $('<div class="neochic-woodlets-modal-content" />');
        contentFrame.appendTo(overlay);
        contentFrame.append(closeButton);
        contentFrame.append(breadCrumbTrail);
        contentFrame.append(content);
        $body.append(overlay);
        $body.addClass('neochic-woodlets-modal-active');
    };

    var close = function() {
        stack.pop();

        if (stack.length > 0) {
            var data = stack.pop();
            open(data.content, data.title);
            return;
        }

        clear();
    };

    overlay.on('click', function(e) {
        if(e.target === this) {
            stack = [];
            close();
        }
    });

    closeButton.on('click', function() {
        close();
    });

    breadCrumbTrail.on('click', 'li[role=button]', function() {
        var toSlice = $(this).nextAll().length - 1;
        stack = stack.slice(0, breadCrumbTrail.children().length - toSlice);
        close();
    });

    return {
        open: open,
        close: close
    };
});
