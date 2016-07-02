hairlov.application.jobService = hairlov.application.jobService || {};

hairlov.application.jobService.booking = {
    init: function()
    {
        this.initCheckboxPayment();
    },
    
    initCheckboxPayment: function()
    {
        $('#other-address-payment').on('change', this.createDisabledInputListener());
    },
    createDisabledInputListener: function()
    {
        return function() {
            $('#address-prestation-payment input').each(function() {
                var $current = $(this);
                if ($current.attr('disabled')) {
                    $current.removeAttr('disabled');
                } else {
                    $current.attr('disabled', 'disabled');
                }
            });
        }
    }
};