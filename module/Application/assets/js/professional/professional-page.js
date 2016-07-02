hairlov.application.professional = hairlov.application.professional || {};

hairlov.application.professional.professionalPage = {
    
    $lovSwitch: null,
    
    init: function()
    {
        this.$lovSwitch = $('.js-like-switch');
        
        hairlov.application.showMorePhotos.init();
        
        this.$lovSwitch.on('click',
            this.createLikeSwitchListener()
        );
    },
    
    createLikeSwitchListener: function()
    {   
        var self = this;
        
        return function(event) {
            event.preventDefault();
            
            var that = $(this);
            
            $.ajax({
                url: '/switch-lov',
                type: 'POST',
                data: {
                    professionalId: self.$lovSwitch.data('professional')
                },
                dataType: 'json',
                success: function(data) {
                    var $span = that.find('span');
                    var oldValue = parseInt($span.text());
                    if(data.action == 'add') {
                        $span.text(oldValue + 1);
                    }
                    else {
                        $span.text(oldValue - 1);
                    }
                },
            });

        };
    }
    
};