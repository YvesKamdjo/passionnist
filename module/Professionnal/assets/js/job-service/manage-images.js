hairlov.professionnal.jobService = hairlov.professionnal.jobService || {};

hairlov.professionnal.jobService.manageImages = {
    init: function(idJobService)
    {
        this.jobServiceImageUploadForm.init(idJobService);
    }
};

/**
 * Formulaire d'upload de la photo de profil
 */
hairlov.professionnal.jobService.manageImages.jobServiceImageUploadForm = {
    /*
     * Références aux éléments du DOM
     */
    $imageUploadSuccessMsg: null,
    $imageUploadRow: null,
    $imageUploadError: null,
    $imageUploadOverlay: null,
    $imageUploadedList: null,
    $body: null,
    
    init: function(idJobService)
    {
        this.idJobService = idJobService;
        
        this.$imageUploadSuccessMsg = $('.js-job-service-image-upload-success');
        this.$imageUploadRow = $('.js-job-service-image-upload-dropzone-row');
        this.$imageUploadError = $('.js-job-service-image-upload-dropzone-error');
        this.$imageUploadOverlay = $('.js-job-service-image-upload-dropzone-overlay');
        this.$jobServiceImageUploadPercentage = $('.js-job-service-image-upload-dropzone-percentage');
        this.$imageUploadedList = $('.js-job-service-image-list');
        this.$body = $('body');
        
        var self = this;
        
        $(".js-job-service-image-upload-dropzone").fileupload({
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
            formData: [ // Données supplémentaires
                {
                    name: "idJobService",
                    value: this.idJobService
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
        
        this.$body.on('click', '.delete-image', this.createDeleteImageListener());
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
    
    createDeleteImageListener: function()
    {
        var self = this;
        
        return function(event) {
            var $button = $(this);
            
            $.ajax({
                url: $button.data('action'),
                type: 'POST',
                dataType: 'json'
            })
            .done(function(result) {
                if (result.success === true) {
                    $button.parent('.thumbnail-with-delete-button').parent().remove();
                }
                else {
                }
            });
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
            self.$jobServiceImageUploadPercentage.text(progress);
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
            
            var newImage = 
                        '<div class="col-xs-3 col-sm-3">'+
                            '<div class="thumbnail-with-delete-button overflow-image galerie-item grey">'+
                                '<button type="button" class="btn btn-danger delete-image" data-action="'+ data.result.action +'" data-toggle="tooltip" data-placement="top" title="Supprimer"><i class="fa fa-trash"></i></button>'+
                                '<img src="/image/job-service-image/'+ data.result.jobServiceImageFilename +'" class="img-responsive"/>'+
                            '</div>'+
                        '</div>';
            // Ajoute à la liste la dernière image uploadée
            self.$imageUploadedList.append(newImage);
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