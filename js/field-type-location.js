/*
 * Initializes a googlemaps location picker for
 * Location field type.
 */

/* globals document, google */

define([
    "jquery",
    "debounce",
], function($, debounce) {
    var geocoder = null;

    function init(form) {
        form = $(form);
        var locationPickers = form.find('.neochic-woodlets-location-picker');

        if (locationPickers.length) {
            requirejs(["jquery-locationpicker", "async!https://maps.googleapis.com/maps/api/js?libraries=places"], function() {
                if (!geocoder) {
                    geocoder = new google.maps.Geocoder();
                }
                
                locationPickers.each(function(i, e) {

                    e = $(e);

                    var valueInput = e.find(".neochic-woodlets-location-value");
                    var searchInput = e.find(".neochic-woodlets-location-search");
                    var changeListenerHelperLat = e.find(".neochic-woodlets-location-change-listener-helper-lat");
                    var changeListenerHelperLng = e.find(".neochic-woodlets-location-change-listener-helper-lng");
                    var mapPane = e.find(".map-pane");
                    var savedData;
                    var initialLocationSetup = true;

                    var streetNumberPreview = e.find("input.streetNumber");
                    var streetNamePreview = e.find("input.streetName");
                    var cityPreview = e.find("input.city");
                    var statePreview = e.find("input.state");
                    var zipPreview = e.find("input.zip");
                    var countryPreview = e.find("input.country");

                    searchInput.on("keypress", function(e) {
                        if (e.keyCode === 13) {
                            e.preventDefault();
                            geocoder.geocode({
                                address: searchInput.val()
                            }, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    var result = results.shift();
                                    if(result !== null) {
                                        mapPane.locationpicker("location", {
                                            latitude: result.geometry.location.lat(),
                                            longitude: result.geometry.location.lng()
                                        });
                                    }
                                }
                            });
                        }
                    });

                    searchInput.on("keyup", function(){
                        if ($.trim(searchInput.val()) === "") {
                            clearData(false);
                        }
                    });

                    try {
                        savedData = JSON.parse(valueInput.val());
                    } catch(exception) {
                        clearData(true);
                    }

                    var gotStartLocation = savedData && savedData.lat && savedData.lng;

                    var startLocation = gotStartLocation ? {
                        latitude: savedData.lat,
                        longitude: savedData.lng
                    } : {
                        // Stuttgart
                        latitude: 48.772292,
                        longitude: 9.168389
                    };

                    mapPane.locationpicker({
                        location: startLocation,
                        radius: 0,
                        zoom: gotStartLocation ? 17 : 5,
                        scrollwheel: true,
                        inputBinding: {
                            locationNameInput: searchInput,
                            latitudeInput: changeListenerHelperLat,
                            longitudeInput: changeListenerHelperLng
                        },
                        enableAutocomplete: true,
                        enableReverseGeocode: true,
                        onchanged: updateLocationData,
                        oninitialized: function(){
                            updateLocationData();

                            var foo = function(){
                                google.maps.event.trigger(mapPane.locationpicker("map").map, 'resize');
                                window.setTimeout(foo, 1000);
                            };

                            foo();
                        }
                    });

                    google.maps.event.addListener(mapPane.locationpicker("map").map, 'click', function (event) {
                        mapPane.locationpicker("location", {
                            latitude: event.latLng.lat(),
                            longitude: event.latLng.lng()
                        });
                    });

                    changeListenerHelperLat.add(changeListenerHelperLng).on("change", debounce(function() {
                        if (initialLocationSetup) {
                            initialLocationSetup = false;
                            if (!gotStartLocation) {
                                clearData(false);
                                return;
                            }
                        }

                        updateLocationData();

                    }, 50));

                    function updateLocationData() {
                        var newData = {};
                        var location = mapPane.locationpicker("map").location;
                        mapPane.locationpicker("map").marker.setVisible(true);

                        newData.streetNumber = location.addressComponents.streetNumber;
                        newData.streetName = location.addressComponents.streetName;
                        newData.city = location.addressComponents.city;
                        newData.state = location.addressComponents.stateOrProvince;
                        newData.zip = location.addressComponents.postalCode;
                        newData.country = location.addressComponents.country;
                        newData.lat = location.latitude;
                        newData.lng = location.longitude;

                        updatePreview(newData);

                        valueInput.val(JSON.stringify(newData));
                    }

                    function updatePreview(newData) {
                        var notAvailable = "-";
                        streetNumberPreview.val(newData.streetNumber ? newData.streetNumber : notAvailable);
                        streetNamePreview.val(newData.streetName ? newData.streetName : notAvailable);
                        cityPreview.val(newData.city ? newData.city : notAvailable);
                        statePreview.val(newData.state ? newData.state : notAvailable);
                        zipPreview.val(newData.zip ? newData.zip : notAvailable);
                        countryPreview.val(newData.country ? newData.country : notAvailable);
                    }

                    function clearData(skipMapUpdate){
                        searchInput.val("");
                        valueInput.val(JSON.stringify({}));
                        savedData = null;
                        updatePreview({});
                        if(!skipMapUpdate) {
                            mapPane.locationpicker("map").marker.setVisible(false);
                        }
                    }

                });
            });
        }
    }

    $(document).on('neochic-woodlets-form-init', function (e, form) {
        init(form);
    });

    $(document).on('neochic-woodlets-modal-unstack', function (e, form) {
        init(form);
    });
});
