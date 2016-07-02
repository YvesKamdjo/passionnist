/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 * @module Global
 */
var hairlov = hairlov || {};


hairlov.navbar = {
    
    $navbarMenuLink: null,
    
    init: function()
    {
        this.$navbarMenuLink = $('#navbar-menu .navbar-nav li.dropdown a');
        
        $(window).scroll(this.createWindowScrollListener());
        this.$navbarMenuLink.on('click', this.createSubmenuListener());
        
        $('.navbar-header > button').on('click', this.collapseSiblings());
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
    },
    
    createSubmenuListener: function() {
        return function (event) {
            if (!$(this).hasClass('profil')
                && $(this).parents('.dropdown-menu').length == 0
            ) {
                event.preventDefault();
                
                var target = $(this).data('target');

                $(target).toggleClass('selected');

                window.location = $('#submenu ul.selected').children('li').first().children('a').attr('href');
            }
        };
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