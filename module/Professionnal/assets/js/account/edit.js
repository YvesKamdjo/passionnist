hairlov.professionnal.account = hairlov.professionnal.account || {};

hairlov.professionnal.account.edit = {
    init: function()
    {
        this.$addressInput = $('.js-address')
        
        this.accountImageUploadForm.init();
        this.qualificationUploadForm.init();
        
        this.addressAutocomplete = new google.maps.places.Autocomplete(
            this.$addressInput[0],
            {
                types: ['geocode'],
                componentRestrictions: {country: 'fr'}
            }
        );

        // Sélection de l'adresse dans l'autocomplete Google
        this.addressAutocomplete.addListener('place_changed',
            this.createAutocompleteAddressSelectedListener()
        );
        
        this.$addressInput.on('keydown', function(event) {
            if (event.which === 13 && $('.pac-container:visible').length) { 
                event.preventDefault(); 
            }
        });
    },
    
    /**
     * 
     * @returns {Function}
     */
    createAutocompleteAddressSelectedListener: function()
    {
        var self = this;
        return function() {
            var place = self.addressAutocomplete.getPlace();
            if (place.geometry) {
                $('.js-address-latitude').val(place.geometry.location.lat());
                $('.js-address-longitude').val(place.geometry.location.lng());
                
                addressComponents = place.address_components;
                
                if (addressComponents.length === 5) {
                    address = addressComponents[0].long_name;
                    zipcode = addressComponents[4].long_name;
                    city = addressComponents[1].long_name;
                }
                else if (addressComponents.length === 6) {
                    address = addressComponents[0].long_name;
                    zipcode = addressComponents[5].long_name;
                    city = addressComponents[1].long_name;
                }
                else if (addressComponents.length === 7) {
                    address = addressComponents[0].long_name + ' ' + addressComponents[1].long_name;
                    zipcode = addressComponents[6].long_name;
                    city = addressComponents[2].long_name;
                }
                
                $('.js-address-address').val(address);
                $('.js-address-zipcode').val(zipcode);
                $('.js-address-city').val(city);
            }
        };
    },
};

/**
 * Formulaire d'upload de la photo de profil
 */
hairlov.professionnal.account.edit.accountImageUploadForm = {
    /*
     * Références aux éléments du DOM
     */
    $imageUploadSuccessMsg: null,
    $imageUploadRow: null,
    $imageUploadError: null,
    $imageUploadOverlay: null,
    $imageUploadedRow: null,
    $accountImageEditButton: null,
    
    init: function()
    {
        this.$imageUploadSuccessMsg = $('.js-account-image-upload-success');
        this.$imageUploadRow = $('.js-account-image-upload-dropzone-row');
        this.$imageUploadedRow = $('.js-account-image-uploaded-row');
        this.$imageUploadError = $('.js-account-image-upload-dropzone-error');
        this.$imageUploadOverlay = $('.js-account-image-upload-dropzone-overlay');
        this.$accountImageUploadPercentage = $('.js-account-image-upload-dropzone-percentage');
        this.$accountImageEditButton = $('.js-show-account-image-edit');
        
        var self = this;
        
        $(".js-account-image-upload-dropzone").fileupload({
            dropZone: self.$imageUploadRow,
            acceptFileTypes: /(\.|\/)(jpe?g|png)$/i,
            dataType: "json",
            maxFileSize: 1000000, // 1MB
            maxNumberOfFiles: 1,
            singleFileUploads: true,
            processQueue: [ // Validation
                {
                    action: "validate",
                    always: true,
                    acceptFileTypes: '@'
                }
            ],
            start: this.createStartUploadListener(),
            progressall: this.createUploadProgressListener(),
            always: function() {
                self.$imageUploadOverlay.addClass('hidden');
            },
            fail: this.createUploadFailedListener(),
            done: this.createUploadFinishedListener()
        }).bind("fileuploadprocessfail", this.createUploadValidationFailedListener());
                
        this.$accountImageEditButton.on('click',
            this.showAccountImageEditForm()
        );
    },
    
    showAccountImageEditForm: function()
    {
        var self = this;
        return function(event) {
            event.preventDefault();
            self.$imageUploadRow.toggleClass('hidden');
            self.$accountImageEditButton.toggleClass('hidden');
            
            return false;
        };
    },
    
    /**
     * Démarrage d'un upload
     * @returns {Function}
     */
    createStartUploadListener: function()
    {
        var self = this;
        return function() {
            // Affiche l'overlay et masque le message de succès
            self.$imageUploadOverlay.removeClass('hidden');
            self.$imageUploadSuccessMsg.addClass('hidden');
            // Vide les erreurs du formulaire
            self.$imageUploadRow.removeClass('has-error');
            self.$imageUploadError.addClass('hidden').html('');
        };
    },
    
    /**
     * Tick de progression de l'upload en cours
     * @returns {Function}
     */
    createUploadProgressListener: function()
    {
        var self = this;
        return function(event, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            self.$accountImageUploadPercentage.text(progress);
        };
    },
    
    /**
     * Échec de l'upload
     * @returns {Function}
     */
    createUploadFailedListener: function()
    {
        var self = this;
        return function(event, data) {
            self.$imageUploadRow.addClass('has-error');
            self.$imageUploadError
                .html("Une erreur est survenue : " + data.jqXHR.statusText + " (code " + data.jqXHR.status + ").")
                .removeClass('hidden');
        };
    },
    
    /**
     * Fin de l'upload
     * @returns {Function}
     */
    createUploadFinishedListener: function()
    {
        var self = this;
        return function(event, data) {
            // Erreur lors du traitement de l'upload
            if (data.result.success !== true) {
                self.$imageUploadRow.addClass('has-error');
                self.$imageUploadError
                    .html(data.result.message)
                    .removeClass('hidden');
                return;
            }
            // Upload terminé avec succès
            self.$imageUploadSuccessMsg.removeClass('hidden');
            
            // Modifie le contenu de l'image
            self.$imageUploadedRow.find('img').attr('src', data.result.avatarFilename);
            self.$imageUploadedRow.removeClass('hidden');
            
            self.$imageUploadRow.toggleClass('hidden');
            self.$accountImageEditButton.toggleClass('hidden');
        };
    },
    
    /**
     * Échec de la validation du formulaire
     * @returns {Function}
     */
    createUploadValidationFailedListener: function()
    {
        var self = this;
        return function(event, data) {
            var message = "Une erreur inattendue est survenue. Si le problème persiste, veuillez contacter notre service client.";
            // Traduction du message d'erreur du plugin jQuery
            switch(data.files[data.index].error) {
                case "Maximum number of files exceeded":
                    message = "Erreur : vous ne pouvez envoyer qu'un seul fichier.";
                    break;
                case "File type not allowed":
                    message = "Erreur : ce type de fichier n'est pas autorisé.";
                    break;
                case "File is too large":
                    message = "Erreur : ce fichier dépasse la limite de poids autorisée (10Mo).";
                    break;
                case "File is too small":
                    message = "Erreur : le poids de ce fichier est trop faible.";
                    break;
            }

            self.$imageUploadRow.addClass('has-error');
            self.$imageUploadError
                .html(message)
                .removeClass('hidden');
        };
    }
    
};

/**
 * Formulaire d'upload du diplôme
 */
hairlov.professionnal.account.edit.qualificationUploadForm = {
    /*
     * Références aux éléments du DOM
     */
    $qualificationUploadSuccessMsg: null,
    $qualificationUploadRow: null,
    $qualificationUploadError: null,
    $qualificationUploadOverlay: null,
    $qualificationUploadedRow: null,
    $qualificationEditButton: null,
    
    init: function()
    {
        this.$qualificationUploadSuccessMsg = $('.js-qualification-upload-success');
        this.$qualificationUploadRow = $('.js-qualification-upload-dropzone-row');
        this.$qualificationUploadedRow = $('.js-qualification-uploaded-row');
        this.$qualificationUploadError = $('.js-qualification-upload-dropzone-error');
        this.$qualificationUploadOverlay = $('.js-qualification-upload-dropzone-overlay');
        this.$accountImageUploadPercentage = $('.js-qualification-upload-dropzone-percentage');
        this.$qualificationEditButton = $('.js-show-qualification-edit');
        
        var self = this;
        
        $(".js-qualification-upload-dropzone").fileupload({
            dropZone: self.$qualificationUploadRow,
            acceptFileTypes: /(\.|\/)(jpe?g|png|pdf)$/i,
            dataType: "json",
            maxFileSize: 10000000, // 10MB
            maxNumberOfFiles: 1,
            singleFileUploads: true,
            processQueue: [ // Validation
                {
                    action: "validate",
                    always: true,
                    acceptFileTypes: '@'
                }
            ],
            start: this.createStartUploadListener(),
            progressall: this.createUploadProgressListener(),
            always: function() {
                self.$qualificationUploadOverlay.addClass('hidden');
            },
            fail: this.createUploadFailedListener(),
            done: this.createUploadFinishedListener()
        }).bind("fileuploadprocessfail", this.createUploadValidationFailedListener());
        
        this.$qualificationEditButton.on('click',
            this.showQualificationEditForm()
        );
    },
    
    showQualificationEditForm: function()
    {
        var self = this;
        return function(event) {
            event.preventDefault();
            self.$qualificationUploadRow.toggleClass('hidden');
            self.$qualificationEditButton.toggleClass('hidden');
            
            return false;
        };
    },
    
    /**
     * Démarrage d'un upload
     * @returns {Function}
     */
    createStartUploadListener: function()
    {
        var self = this;
        return function() {
            // Affiche l'overlay et masque le message de succès
            self.$qualificationUploadOverlay.removeClass('hidden');
            self.$qualificationUploadSuccessMsg.addClass('hidden');
            // Vide les erreurs du formulaire
            self.$qualificationUploadRow.removeClass('has-error');
            self.$qualificationUploadError.addClass('hidden').html('');
        };
    },
    
    /**
     * Tick de progression de l'upload en cours
     * @returns {Function}
     */
    createUploadProgressListener: function()
    {
        var self = this;
        return function(event, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            self.$accountImageUploadPercentage.text(progress);
        };
    },
    
    /**
     * Échec de l'upload
     * @returns {Function}
     */
    createUploadFailedListener: function()
    {
        var self = this;
        return function(event, data) {
            self.$qualificationUploadRow.addClass('has-error');
            self.$qualificationUploadError
                .html("Une erreur est survenue : " + data.jqXHR.statusText + " (code " + data.jqXHR.status + ").")
                .removeClass('hidden');
        };
    },
    
    /**
     * Fin de l'upload
     * @returns {Function}
     */
    createUploadFinishedListener: function()
    {
        var self = this;
        return function(event, data) {
            // Erreur lors du traitement de l'upload
            if (data.result.success !== true) {
                self.$qualificationUploadRow.addClass('has-error');
                self.$qualificationUploadError
                    .html(data.result.message)
                    .removeClass('hidden');
                return;
            }
            // Upload terminé avec succès
            self.$qualificationUploadSuccessMsg.removeClass('hidden');
            self.$qualificationUploadRow.toggleClass('hidden');
            self.$qualificationEditButton.toggleClass('hidden');
            
            var $qualificationPreviewButton = $('.js-qualification-preview');
            // Modifie le contenu de l'image
            
            if ($qualificationPreviewButton.attr('href')) {
                $qualificationPreviewButton.attr('href', data.result.qualificationFilename);
            }
            else {
                self.$qualificationUploadSuccessMsg.after(
                    '<div class="box box-content clearfix">'+
                        '<div>'+
                            '<div class="pull-left " style="line-height: 38px;">'+
                                '<b>Un diplôme à déjà été ajouté</b>'+
                            '</div>'+
                            '<div class="pull-right">'+
                                '<a class="btn btn-default js-qualification-preview" target="_blank" href="'+data.result.qualificationFilename+'">Voir mon diplôme</a>'+
                                '<button class="btn btn-primary js-show-qualification-edit">Modifier mon diplôme</button>'+
                            '</div>'+
                        '</div>'+
                    '</div>'
                );
            }
        };
    },
    
    /**
     * Échec de la validation du formulaire
     * @returns {Function}
     */
    createUploadValidationFailedListener: function()
    {
        var self = this;
        return function(event, data) {
            var message = "Une erreur inattendue est survenue. Si le problème persiste, veuillez contacter notre service client.";
            // Traduction du message d'erreur du plugin jQuery
            switch(data.files[data.index].error) {
                case "Maximum number of files exceeded":
                    message = "Erreur : vous ne pouvez envoyer qu'un seul fichier.";
                    break;
                case "File type not allowed":
                    message = "Erreur : ce type de fichier n'est pas autorisé.";
                    break;
                case "File is too large":
                    message = "Erreur : ce fichier dépasse la limite de poids autorisée (10Mo).";
                    break;
                case "File is too small":
                    message = "Erreur : le poids de ce fichier est trop faible.";
                    break;
            }

            self.$qualificationUploadRow.addClass('has-error');
            self.$qualificationUploadError
                .html(message)
                .removeClass('hidden');
        };
    }
    
};