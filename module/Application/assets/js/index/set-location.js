hairlov.application.index = hairlov.application.index || {};

hairlov.application.index.setLocation = {
    
    $addressInput: null,
    
    init: function()
    {
        this.$addressInput = $('.js-address-location-city');
        
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
            $('.pac-container').css('z-index', 1050);
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
                $('.js-location-latitude').val(place.geometry.location.lat());
                $('.js-location-longitude').val(place.geometry.location.lng());
                
                addressComponents = place.address_components;
                                
                if (addressComponents.length === 4) {
                    city = addressComponents[0].long_name;
                }
                else if (addressComponents.length === 5) {
                    city = addressComponents[0].long_name;
                }
                else if (addressComponents.length === 6) {
                    city = addressComponents[1].long_name;
                }
                else if (addressComponents.length === 7) {
                    city = addressComponents[2].long_name;
                }
                
                $('.js-location-city').val(city);
            }
        };
    },
    
};