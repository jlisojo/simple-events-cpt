(function ($) {
    function syncFreePricing() {
        var isFree = $('#se_event_is_free').is(':checked');
        $('#se_event_price, #se_event_price_child, #se_event_price_adult')
            .prop('readonly', isFree);

        if (isFree) {
            $('#se_event_price, #se_event_price_child, #se_event_price_adult').each(function () {
                if (!$(this).val()) {
                    $(this).val('0');
                }
            });
        }
    }

    $(document).on('change', '#se_event_is_free', syncFreePricing);
    syncFreePricing();
})(jQuery);
