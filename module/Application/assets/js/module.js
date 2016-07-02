/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 * @module Application
 */
var hairlov = hairlov || {};

hairlov.application = {
    init: function()
    {
        this.stickyNavbar.init();
        //this.payment.init();
        
        $('.navbar-header > button').on('click', this.collapseSiblings());
    },
    
    collapseSiblings: function() {
        console.log('test2');
        return function (event) {
            $brothers = $(this).siblings();
            
            $brothers.each(function() {
                $($(this).data('target')).removeClass('in');
            });
        };
    }
};

hairlov.application.stickyNavbar = {
    
    init: function()
    {
        $(window).scroll(this.createWindowScrollListener());
    },
    
    createWindowScrollListener: function() {
        return function(){
            var $navbar = $('#header + .navbar');
            var $header = $('#header');
            var $scroll = $(window).scrollTop();

            if ($scroll >= $header.outerHeight()) {
                $('body').css('padding-top', $navbar.outerHeight());
                $navbar.addClass('navbar-fixed-top bordered');
            }
            else {
                $('body').css('padding-top', '0px');
                $navbar.removeClass('navbar-fixed-top bordered');
            }
        };
    }
};

hairlov.application.showMorePhotos = {
    
    init: function()
    {
        $('#js-show-more-photo').on('click', this.createdisplayPhotoListener());
    },
    
    createdisplayPhotoListener: function(event) {
        return function (event) {
            event.preventDefault();
        
            var $current = $(this);
            $current.closest('.row').find('.hidden').hide().removeClass('hidden').fadeIn();
            $current.remove();
        }
    }
};