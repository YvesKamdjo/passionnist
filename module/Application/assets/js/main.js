/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 * @module Application
 */
$(document).ready(function() {
    /**
     * Référence à l'élément body
     * @type Object
     */
    var $body = $('body');
    /**
     * Témoin de chargement du formulaire de login
     * @type Boolean
     */
    var isLogInFormLoading = false;
    /**
     * Témoin de chargement du formulaire d'inscription pro
     * @type Boolean
     */
    var isProSignUpFormLoading = false;
    
    // Fermeture d'une modale au clic sur le bouton de fermeture
    $body.on('click', '.js-modal-close', function(event) {
        event.preventDefault();
        $(this).parents('.js-modal').removeClass('is-visible');
    });
    
    // Affichage du formulaire de connexion
    $body.on('click', '.js-show-login-form', function(event) {
        event.preventDefault();
        $('.js-login-form').addClass('is-visible');
        $('#login-email').focus();
    });
    
    $body.on('click', '.js-signup-form-submit', function(event) {
        $('.js-is-signup').val(1);
    });
    
    // Soumission du formulaire de login
    $('#logInForm').on('submit', function(event) {
        event.preventDefault();
        var $form = $(this);
        var $submitButton = $('.js-login-form-submit');
        var $errorMessage = $('.js-login-form-error');
        var $formRows = $('.js-login-form-row');
        
        // Bloque l'accès si le formulaire est déjà en cours de chargement
        if (isLogInFormLoading === true) {
            return false;
        }
        isLogInFormLoading = true;
        
        // Affiche le texte de chargement
        $submitButton.data('original-text', $submitButton.text());
        $submitButton.text("Connexion …");
        // Vide et masque le message d'erreur
        $errorMessage.html('').addClass('hidden');
        // Retire le statut d'erreur des champs
        $formRows.removeClass('has-error');
        
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json'
        })
        .always(function() {
            isLogInFormLoading = false;
            $submitButton.text($submitButton.data('original-text'));
        })
        .done(function(result) {
            if (result.success === true) {
                window.location.replace(result.route);
                $submitButton.text("Bienvenue !");
            }
            else {
                $errorMessage.html(result.message).removeClass('hidden');
                $formRows.addClass('has-error');
            }
        })
        .fail(function() {
            $errorMessage.html("Identifiants invalides").removeClass('hidden');
            $formRows.addClass('has-error');
        });
    });
    
    // Affichage du formulaire d'inscription pro
    $body.on('click', '.js-show-pro-signup-form', function(event) {
        event.preventDefault();
        $('.js-pro-signup-form').addClass('is-visible');
        $('#signup-lastname').focus();
    });
    
    // Soumission du formulaire d'inscription pro
    $('#professionnalSignUpForm').on('submit', function(event) {
        event.preventDefault();
        var $form = $(this);
        var $submitButton = $('.js-pro-signup-form-submit');
        var $errorMessage = $('.js-pro-signup-form-error');
        var $formRows = $('.js-pro-signup-form-row');
        
        // Bloque l'accès si le formulaire est déjà en cours de chargement
        if (isProSignUpFormLoading === true) {
            return false;
        }
        isProSignUpFormLoading = true;
        
        // Affiche le texte de chargement
        $submitButton.data('original-text', $submitButton.text());
        $submitButton.text("Connexion …");
        // Vide et masque les messages d'erreur
        $errorMessage.html('').addClass('hidden');
        $('.js-pro-signup-form-input-error').remove();
        // Retire le statut d'erreur des champs
        $formRows.removeClass('has-error');
        
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json'
        })
        .always(function() {
            isProSignUpFormLoading = false;
            $submitButton.text($submitButton.data('original-text'));
        })
        .done(function(result) {
            if (result.success === true) {
                window.location.replace(result.route);
                $submitButton.text("Bienvenue !");
            }
            else {
                $errorMessage.html(result.message).removeClass('hidden');
                
                if (result.hasOwnProperty('errors')) {
                    if (result.errors.hasOwnProperty('first-name')) {
                        for (var index in result.errors['first-name']) {
                            $('#signup-firstname').after('<div class="help-block js-pro-signup-form-input-error">' + result.errors['first-name'][index] + '</div>');
                            $('#signup-firstname').parent().addClass('has-error');
                        }
                    }
                    if (result.errors.hasOwnProperty('last-name')) {
                        for (var index in result.errors['last-name']) {
                            $('#signup-lastname').after('<div class="help-block js-pro-signup-form-input-error">' + result.errors['last-name'][index] + '</div>');
                            $('#signup-lastname').parent().addClass('has-error');
                        }
                    }
                    if (result.errors.hasOwnProperty('password')) {
                        for (var index in result.errors.password) {
                            $('#signup-password').after('<div class="help-block js-pro-signup-form-input-error">' + result.errors.password[index] + '</div>');
                            $('#signup-password').parent().addClass('has-error');
                        }
                    }
                    if (result.errors.hasOwnProperty('phone')) {
                        for (var index in result.errors.phone) {
                            $('#signup-phone').after('<div class="help-block js-pro-signup-form-input-error">' + result.errors.phone[index] + '</div>');
                            $('#signup-phone').parent().addClass('has-error');
                        }
                    }
                    if (result.errors.hasOwnProperty('email')) {
                        for (var index in result.errors.email) {
                            $('#signup-email').after('<div class="help-block js-pro-signup-form-input-error">' + result.errors.email[index] + '</div>');
                            $('#signup-email').parent().addClass('has-error');
                        }
                    }
                    if (result.errors.hasOwnProperty('account-type')) {
                        for (var index in result.errors['account-type']) {
                            $('#signup-account-type').after('<div class="help-block js-pro-signup-form-input-error">' + result.errors.email[index] + '</div>');
                            $('#signup-account-type').parent().addClass('has-error');
                        }
                    }
                    if (result.errors.hasOwnProperty('referral')) {
                        for (var index in result.errors.referral) {
                            $('#signup-referral').after('<div class="help-block js-pro-signup-form-input-error">' + result.errors.referral[index] + '</div>');
                            $('#signup-referral').parent().addClass('has-error');
                        }
                    }
                    if (result.errors.hasOwnProperty('terms')) {
                        for (var index in result.errors.terms) {
                            $('.js-signup-terms-input').after('<div class="help-block js-pro-signup-form-input-error">' + result.errors.terms[index] + '</div>');
                            $('.js-signup-terms-input').parent().addClass('has-error');
                        }
                    }
                }
            }
        })
        .fail(function() {
            $errorMessage.html("Identifiants invalides").removeClass('hidden');
            $formRows.addClass('has-error');
        });
    });
    
    // Affiche le formulaire d'inscription si des données sont déjà remplies
    if ($('.js-pro-signup-email-input').val() != '') {
        $('.js-pro-signup-form').addClass('is-visible');
    }
    if ($('.js-pro-signup-account-type-input').val() != ''
        && $('.js-pro-signup-account-type-input').val() !== null
    ) {
        $('.js-pro-signup-form').addClass('is-visible');
    }
});