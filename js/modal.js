/*
 * Trivial modal to replace Thickbox from WordPress,
 * since it doesn't work as expected.
 */

define(['jquery'], function($) {
    var overlay = $('<div class="neochic-woodlets-modal" />');
    var contentFrame = $('<div class="neochic-woodlets-modal-content" />');
    var closeButton = $('<div class="tb-close-icon">Close</div>');
    var $body = $('body');

    contentFrame.appendTo(overlay);

    var open = function(content) {
        close();
        contentFrame.html('');
        contentFrame.append(closeButton);
        contentFrame.append(content);
        $body.append(overlay);
        $body.addClass('neochic-woodlets-modal-active');
    };

    var close = function() {
        closeButton.detach();
        overlay.detach();
        $body.removeClass('neochic-woodlets-modal-active');
    };

    overlay.on('click', function(e) {
        if(e.target === this) {
            close();
        }
    });

    closeButton.on('click', function() {
        close();
    });

    return {
        open: open,
        close: close
    };
});
