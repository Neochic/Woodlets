/* globals document */

define([], function() {
    return function(input) {
        var evt = document.createEvent('Events');
        evt.initEvent('change', true, false);
        input.dispatchEvent(evt);
    };
});
