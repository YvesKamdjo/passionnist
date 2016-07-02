hairlov.professionnal.salon = hairlov.professionnal.salon || {};

hairlov.professionnal.salon.edit = {
    /*
     * Attributs de la classe
     */
    idSalon: null,
    
    /*Ouai
     * Références aux éléments du DOM
     */
    $certificateUploadSuccessMsg: null,
    $certificateUploadRow: null,
    $certificateUploadError: null,
    $certificateUploadOverlay: null,
    $addressInput: null,
    $certificatePreviewButton: null,
    $certificateEditButton: null,
    
    /**
     * @param {Integer} idSalon ID du salon sur lequel le formulaire doit agir
     */
    init: function(idSalon)
    {
        this.idSalon = idSalon;
        
        this.$certificateUploadSuccessMsg = $('.js-certificate-upload-success');
        this.$certificateUploadRow = $('.js-certificate-upload-dropzone-row');
        this.$certificateUploadError = $('.js-certificate-upload-dropzone-error');
        this.$certificateUploadOverlay = $('.js-certificate-upload-dropzone-overlay');
        this.$certificateUploadPercentage = $('.js-certificate-upload-dropzone-percentage');
        this.$addressInput = $('.js-address');
        this.$certificatePreviewButton = $('.js-certificate-preview');
        this.$certificateEditButton = $('.js-show-certificate-edit');
        
        this.initCertificateUpload();
        
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
    
    initCertificateUpload: function()
    {
        var self = this;
        
        $(".js-certificate-upload-dropzone").fileupload({
            dropZone: self.$certificateUploadRow,
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
            formData: [ // Données supplémentaires
                {
                    name: "idSalon",
                    value: this.idSalon
                }
            ],
            start: this.createStartUploadListener(),
            progressall: this.createUploadProgressListener(),
            always: function() {
                self.$certificateUploadOverlay.addClass('hidden');
            },
            fail: this.createUploadFailedListener(),
            done: this.createUploadFinishedListener()
        }).bind("fileuploadprocessfail", this.createUploadValidationFailedListener());
        
        this.$certificateEditButton.on('click',
            this.showCertificateEditForm()
        );
    },
    
    showCertificateEditForm: function()
    {
        var self = this;
        return function(event) {
            event.preventDefault();
            self.$certificateUploadRow.toggleClass('hidden');
            self.$certificateEditButton.toggleClass('hidden');
            
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
            self.$certificateUploadOverlay.removeClass('hidden');
            self.$certificateUploadSuccessMsg.addClass('hidden');
            // Vide les erreurs du formulaire
            self.$certificateUploadRow.removeClass('has-error');
            self.$certificateUploadError.addClass('hidden').html('');
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
            self.$certificateUploadPercentage.text(progress);
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
            self.$certificateUploadRow.addClass('has-error');
            self.$certificateUploadError
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
                self.$certificateUploadRow.addClass('has-error');
                self.$certificateUploadError
                    .html(data.result.success.message)
                    .removeClass('hidden');
                return;
            }
            // Upload terminé avec succès
            self.$certificateUploadSuccessMsg.removeClass('hidden');
            
            self.$certificateEditButton.toggleClass('hidden');
            self.$certificateUploadRow.toggleClass('hidden');
            
            if (self.$certificatePreviewButton.attr('href')) {
                self.$certificatePreviewButton.attr('href', data.result.certificateFilename);
            }
            else {
                self.$certificateUploadSuccessMsg.after(
                    '<div class="box box-content clearfix">'+
                        '<div>'+
                            '<div class="pull-left " style="line-height: 38px;">'+
                                '<b>Un K-bis à déjà été ajouté</b>'+
                            '</div>'+
                            '<div class="pull-right">'+
                                '<a class="btn btn-default js-certificate-preview" target="_blank" href="'+data.result.certificateFilename+'">Voir mon K-bis</a>'+
                                '<button class="btn btn-primary js-show-certificate-edit">Modifier mon K-bis</button>'+
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

            self.$certificateUploadRow.addClass('has-error');
            self.$certificateUploadError
                .html(message)
                .removeClass('hidden');
        };
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