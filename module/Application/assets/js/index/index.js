hairlov.application.index = hairlov.application.index || {};

hairlov.application.index.index = {
    
    $body: null,
    
    init: function()
    {
        this.$body = $('body');
        
        this.$body.on('click',
            '.pinterest-more',
            this.createShowMoreFashionImagesListener()
        );
    },
    
    createShowMoreFashionImagesListener: function(clickEvent)
    {
        var self = this;
        
        return function(event) {
            var $button = $(this);
            var $week = $button.data('week');
            var $year = $button.data('year');
            var $page = $button.data('page');

            $.ajax({
                url: '/load-more-fashion-images',
                type: 'POST',
                data: {
                    week: $week,
                    year: $year,
                    page: $page
                },
                dataType: 'html'
            })
            .done(function(result) {
                $button.parents('.pinterest').html(result);
            });

            event.preventDefault();
        };
    }
    
};