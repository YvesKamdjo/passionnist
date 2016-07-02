hairlov.professionnal.salon = hairlov.professionnal.salon || {};

hairlov.professionnal.salon.create = {
        
    $addressInput: null,
        
    init: function()
    {
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