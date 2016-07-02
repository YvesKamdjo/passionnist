hairlov.application.index = hairlov.application.index || {};

hairlov.application.index.customerLandingPage = {
    
    $facebookLoginButton: null,
    
    $facebookLoginForm: null,
    
    $facebookLoginAccessTokenElement: null,
    
    init: function(facebookAppId)
    {
        this.facebookAppId = facebookAppId;
        this.$facebookLoginButton = $('.js-facebook-login');
        this.$facebookLoginForm = $('.js-facebook-log-in-form');
        this.$facebookLoginAccessTokenElement = $('.js-facebook-log-in-access-token');
        
        // Load the SDK asynchronously
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        
        var self = this;
        
        window.fbAsyncInit = function() {
            FB.init({
                appId      : self.facebookAppId,
                cookie     : true,
                xfbml      : true,
                version    : 'v2.5'
            });

        };
        
        this.$facebookLoginButton.on('click',
            this.createShowFacebookLoginListener()
        );
    },
    
    createShowFacebookLoginListener: function()
    {
        var self = this;
        
        return function(event) {
            FB.login(function(response) {

                if (response.authResponse) {
                    
                    self.$facebookLoginAccessTokenElement.val(response.authResponse.accessToken);
                    self.$facebookLoginForm.submit();

                }
            }, {
                scope: 'public_profile,email'
            });

            event.preventDefault();
        };
    }
    
};