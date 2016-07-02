hairlov.application.jobService = hairlov.application.jobService || {};

hairlov.application.jobService.search = {
    
    $document: null,
    $datepicker: null,
    $dropdownMenuForm: null,
    
    
    addressAutocomplete: null,
    isFormLocked: false,
        
    $form: null,
    $addressInput: null,
    
    init: function()
    {
        this.$document = $(document);
        this.$datepicker = $('.datepicker-search');
        this.$dropdownMenuForm = $('.dropdown-menu-form');
        
        // Instancie un datepicker
        this.initDatepicker();
        
        // Instancie les filtres sous forme de dropdown
        this.initSearchDropdown();
        
        this.$form = $('.js-search-form');
        this.$addressInput = $('.js-address');
        
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
     * Nouvelle instance du datepicker sur le selecteur .datepicker-search
     * @returns {Function}
     */ 
    initDatepicker: function()
    {
        var nowDate = new Date();
        var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
        
        this.$datepicker.datepicker({
            language: 'fr',
            startDate: today,
            autoclose: true,
        });
        
        this.$datepicker.datepicker().on('changeDate', this.createSelectDateListener());
    },
    
    /**
     * Gestion de l'événement sur le changement de la date du datepicker
     * @returns {Function}
     */ 
    createSelectDateListener: function ()
    {
        return function(event) {
            var d = event.date;
            var date = d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
            $('input[name="date_hidden"]').val(date);
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
                $('.js-latitude').val(place.geometry.location.lat());
                $('.js-longitude').val(place.geometry.location.lng());
            }
        };
    },
    
    /**
     * Gestion des dropdown sur les filtres de type checkbox et radio
     */
    initSearchDropdown: function()
    {
        this.preventClick();
        this.$dropdownMenuForm.on('change', 'input:checkbox', this.createShowDropdownLengthListener());
        this.$dropdownMenuForm.on('change', 'input:radio', this.createShowDropdownLengthListener('radio'));
    },
    
    preventClick: function()
    {
        this.$document.on('click', '.dropdown-menu.dropdown-menu-form', function(event) {
            event.stopPropagation();
        });
    },
    
    createShowDropdownLengthListener: function(type)
    {
        return function() {
            var type = type || 'checkbox';
            var parent = $(this).closest('.dropdown-menu-form');
            var $input = parent.find('input:' + type + ':checked');
            if(type === 'radio') {
                $input = $input.not('input:radio[value="0"]');
            }
            
            var len = $input.length;

            if (len > 0) {
                parent.closest('.btn-group').find('button .counter').text('(' + len + ')');
            }
            else {
                parent.closest('.btn-group').find('button .counter').text(' ');
            }
        };
    }
};