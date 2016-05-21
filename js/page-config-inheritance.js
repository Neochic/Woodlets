define(['jquery'], function($) {
    $(document).on('change keyup input propertychange', '[id^=woodlets_page_setting_]:not(.inherit-input)', function() {
        $('#' + $(this).attr('id') + '_inherit').prop('checked', false);
    });
});
