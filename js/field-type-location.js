/*
 * Initializes a googlemaps location picker for
 * Location field type.
 */

/* globals document, google, window, alert */

define([
    "jquery",
    "debounce",
    "modal",
    "native-change",
    "async"
], function($, debounce, modal, nativeChange) {

    // reusable stuff (performance increase incase of several locationpickers on one page)
    var geocoder = null;
    var map = null;
    var overlayContent = null;
    var searchInput = null;
    var changeListenerHelperLat = null;
    var changeListenerHelperLng = null;
    var mapPane = null;
    var saveButton = null;
    var detectButton = null;
    var cancelButton = null;
    var overlayValueInput = null;
    var overlayPreviewContainer = null;
    var newData = {};
    var stgt = {
        // Stuttgart
        latitude: 48.772292,
        longitude: 9.168389
    };

    var geoLocApiLatLng = null;

    function getGeoLocApi() {
        return window.navigator.geolocation;
    }

    function updateGeoLocApiLatLng() {
        var geoLocApi = getGeoLocApi();
        var deferred = $.Deferred();
        if (geoLocApi) {
            geoLocApi.getCurrentPosition(function (position) {
                geoLocApiLatLng = {
                    lat: parseFloat(position.coords.latitude),
                    lng: parseFloat(position.coords.longitude)
                };
                deferred.resolve(geoLocApiLatLng);
            }, function(){
                deferred.reject("Access to the Gelocation API has been denied by the browser.");
            });
        } else {
            deferred.reject("Gelocation API not available.");
        }
        return deferred;
    }

    function init(form) {

        form = $(form);

        form.find('.neochic-woodlets-location-picker:not(.initialized)').each(function(i, lpc) {
            lpc = $(lpc);
            var apiKey = lpc.data("api-key");

            var openOverlayButton = lpc.find(".neochic-woodlets-location-pick-location");
            var previewContainer = lpc.children(".preview-container");

            if (!apiKey) {
                openOverlayButton.add(previewContainer).on("click", function() {
                    alert("For the location picker to work, you must set a Google Maps API Key in your wp-config:\r\ndefine('GOOGLE_MAPS_API_KEY', 'YOUR_API_KEY');\r\n");
                });
                return;
            }

            lpc.addClass('initialized');
            var defaultLat = lpc.data("default-lat");
            var defaultLng = lpc.data("default-lng");
            var center = (defaultLat && defaultLng) ? {
                latitude: defaultLat,
                longitude: defaultLng
            } : stgt;

            if (!overlayContent) {
                overlayContent = $(lpc.data("overlay-content"));
                searchInput = overlayContent.find(".neochic-woodlets-location-search");
                changeListenerHelperLat = overlayContent.find(".neochic-woodlets-location-change-listener-helper-lat");
                changeListenerHelperLng = overlayContent.find(".neochic-woodlets-location-change-listener-helper-lng");
                mapPane = overlayContent.find(".map-pane");
                overlayValueInput = overlayContent.find(".neochic-woodlets-location-overlay-value");
                saveButton = overlayContent.find(".button.save");
                cancelButton = overlayContent.find(".button.cancel");
                overlayPreviewContainer = overlayContent.find(".neochic_container");
                detectButton = overlayContent.find(".detect");
                if (getGeoLocApi()) {
                    detectButton.css({display: "block"});
                }
            }

            var valueInput = lpc.find(".neochic-woodlets-location-value");
            var locationData;
            var initialLocationSetup = true;

            try {
                locationData = JSON.parse(valueInput.val());
                updatePreview(locationData, previewContainer);
            } catch(exception) {
                clearData(previewContainer);
            }

            var gotStartLocation;
            var startLocation;

            defaultLocation(locationData);

            openOverlayButton.add(previewContainer).on("click", function(e) {
                initialLocationSetup = true;
                e.preventDefault();
                modal.open(overlayContent, lpc.data("title"));
                requirejs(["async!https://maps.googleapis.com/maps/api/js?key="+apiKey+"&libraries=places"], function() {
                    requirejs(["jquery-locationpicker"], function() {

                        if (!map) {

                            geocoder = new google.maps.Geocoder();

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
                                    clearData(overlayPreviewContainer);
                                }
                            });

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
                                addressFormat: "street_number",
                                enableAutocomplete: true,
                                enableReverseGeocode: true,
                                onchanged: updateLocationData,
                                oninitialized: function(){
                                    updateLocationData();
                                    map = mapPane.locationpicker("map").map;

                                    google.maps.event.addListener(map, 'click', function (event) {
                                        mapPane.locationpicker("location", {
                                            latitude: event.latLng.lat(),
                                            longitude: event.latLng.lng()
                                        });
                                    });

                                }
                            });

                            changeListenerHelperLat.add(changeListenerHelperLng).on("change", debounce(function() {
                                if (initialLocationSetup) {
                                    initialLocationSetup = false;
                                    if (!gotStartLocation) {
                                        clearData(overlayPreviewContainer);
                                        return;
                                    }
                                }

                                updateLocationData();

                            }, 50));

                            cancelButton.on("click", function(){

                                modal.close();
                            });

                        } else {
                            if(gotStartLocation) {
                                mapPane.locationpicker("location", startLocation);
                                map.setZoom(17);
                            } else {
                                mapPane.locationpicker("location", startLocation);
                                clearData(overlayPreviewContainer);
                                map.setCenter({
                                    lat: startLocation.latitude,
                                    lng: startLocation.longitude
                                });
                                map.setZoom(5);
                            }
                        }
                        saveButton.off("click");
                        saveButton.on("click", function(){
                            valueInput.val(overlayValueInput.val());
                            var tmp = JSON.parse(valueInput.val());
                            updatePreview(tmp, previewContainer);
                            defaultLocation(tmp);
                            modal.close();
                            nativeChange(valueInput.get(0));
                        });

                        detectButton.off("click");
                        detectButton.on("click", function(){
                            updateGeoLocApiLatLng().then(function(position){
                                mapPane.locationpicker("location", {
                                    latitude: position.lat,
                                    longitude: position.lng
                                });
                            }, function(reason){
                                alert(reason);
                            });
                        });

                    });
                });
            });

            function defaultLocation(data) {
                gotStartLocation = !!(data && data.lat && data.lng);
                startLocation = gotStartLocation ? {
                    latitude: data.lat,
                    longitude: data.lng
                } : center;
            }
        });
    }

    function clearData(container){
        searchInput.val("");
        overlayValueInput.val(JSON.stringify({}));
        updatePreview({}, container);
        if (map) {
            mapPane.locationpicker("map").marker.setVisible(false);
        }
    }

    function updateLocationData() {
        newData = {};
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

        updatePreview(newData, overlayPreviewContainer);

        overlayValueInput.val(JSON.stringify(newData));
    }

    function updatePreview(data, container) {

        $.each(["streetNumber", "streetName", "city", "state", "zip", "country"], function(key, val) {
            var target = container.find("." + val);
            if (target.is("input")) {
                target.val(data[val] ? data[val] : "-");
                return;
            }
            target.text(data[val] ? data[val] : "");
        });

        if (container.is('.preview-container')) {
            container.children('div').each(function() {
                $(this).removeClass('hidden');
                if ($(this).children(':not(:empty)').length === 0) {
                    $(this).addClass('hidden');
                }
            });

            container.toggleClass('has-location', container.children('div:not(.hidden)').length > 0);
        }
    }

    $(document).on('neochic-woodlets-form-init', function (e, form) {
        init(form);
    });

    $(document).on('neochic-woodlets-modal-unstack', function (e, form) {
        init(form);
    });
});
