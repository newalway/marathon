//Google geocoded location
function modal_initGeocodeMap()
{
    var objLatLng = modal_getElementLatLng();
    modal_setGoogleMap(objLatLng.lat, objLatLng.lng);
}

function modal_setGoogleMap(lat, lng, is_set_geocode)
{
    is_set_geocode = (typeof is_set_geocode !== 'undefined') ?  is_set_geocode : 0;

    var mapLatLng = new google.maps.LatLng( lat, lng ),
    map = new google.maps.Map(document.getElementById('map-canvas-modal'), {
        zoom: 15,
        center: mapLatLng
    }),
    geocoder = new google.maps.Geocoder(),
    marker = new google.maps.Marker({
        map: map,
        position: mapLatLng,
        draggable: true
    });

    var infowindow = new google.maps.InfoWindow;

    //set data when have lat, lng from gps
    if(is_set_geocode){
        //set input value
        modal_setElementLatLng(lat, lng);
        //set formated address
        modal_geocodeFormattedAddress(geocoder, map, marker, infowindow);
    }

    //Event listenner click search
    document.getElementById('submit-map-modal').addEventListener('click', function() {
        modal_geocodeSearchAddress(geocoder, map, marker, infowindow);
    });

    //Event listenner draged marker
    google.maps.event.addListener(marker, 'dragend', function (event) {
        //set input value
        modal_setElementLatLng(this.getPosition().lat(), this.getPosition().lng());
        //set formated address
        modal_geocodeFormattedAddress(geocoder, map, marker, infowindow);
    });

    //Event listenner change lat, lng
    document.getElementById('modal_latitude').addEventListener('change', function() {
        modal_geocodeSetPositonFromLatLng(geocoder, map, marker, infowindow);
    });
    document.getElementById('modal_longitude').addEventListener('change', function() {
        modal_geocodeSetPositonFromLatLng(geocoder, map, marker, infowindow);
    });
}

function modal_geocodeSearchAddress(geocoder, resultsMap, marker, infowindow) {
    var address = $('#geocode-address-modal').val();
    geocoder.geocode({'address': address}, function(results, status) {
        if (status === 'OK') {
            //move to
            resultsMap.setCenter(results[0].geometry.location);
            //set marker
            marker.setPosition(results[0].geometry.location);
            //set input value
            modal_setElementLatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());

            //set place_id
            modal_setElementPlaceId(results[0].place_id);

// console.log('modal search address');
// console.log(results[0].address_components);

            //set address field
            modal_setElementAddressComponents(results[0].address_components);

            //set formated address
            infowindow.setContent(results[0].formatted_address);
            infowindow.open(resultsMap, marker);

        } else {
            alert('Search geocode was not successful for the following reason: ' + status);
        }
    });
}

function modal_geocodeFormattedAddress(geocoder, resultsMap, marker, infowindow) {
    var pos = marker.getPosition();
    geocoder.geocode({
        latLng: pos
    }, function(responses) {
        if (responses && responses.length > 0) {

            //set formated address
            infowindow.setContent(responses[0].formatted_address);
            infowindow.open(resultsMap, marker);

            //set place_id
            modal_setElementPlaceId(responses[0].place_id);

// console.log('modal format address');
// console.log(responses[0].address_components);

            //set address field
            modal_setElementAddressComponents(responses[0].address_components);

        } else {
            //Cannot determine address at this location.
            infowindow.setContent('');
            infowindow.close();
        }
    });
}

function modal_geocodeSetPositonFromLatLng(geocoder, resultsMap, marker, infowindow)
{
    var localObjLatLng = modal_getElementLatLng();
    var localMapLatLng = new google.maps.LatLng( localObjLatLng.lat, localObjLatLng.lng );
    resultsMap.setCenter(localMapLatLng);
    //set marker
    marker.setPosition(localMapLatLng);
    //set formated address
    modal_geocodeFormattedAddress(geocoder, resultsMap, marker, infowindow);
}

function modal_setElementPlaceId(place_id)
{
    $('#modal_placeId').val(place_id);
}

function modal_setElementLatLng(lat,lng)
{
    $('#modal_latitude').val(lat);
    $('#modal_longitude').val(lng);
}

function modal_getElementLatLng()
{
    //Default is Bangkok
    var lat = $('#modal_latitude').val() || 13.756330900000009,
    lng = $('#modal_longitude').val() || 100.50176510000006;
    return {"lat":lat, 'lng':lng};
}

function modal_setElementAddressComponents(address_components)
{
    // console.log('Start set addressComponents ');
    // components_length = address_components.length;

    modal_resetElementAddressComponents();

    var address_value = '',
        district_value = '',
        amphure_value = '',
        province_value = '',
        postcode_value = '';

    address_components.forEach(function(addr) {
        arr_types = addr.types
        addr_data = addr.long_name;

        if(  (arr_types.indexOf("premise") != -1) || (arr_types.indexOf("establishment") != -1) || (arr_types.indexOf("point_of_interest") != -1) || (arr_types.indexOf("airport") != -1) || (arr_types.indexOf("park") != -1)  ) {
            address_value = addr_data;
        }else if(arr_types.indexOf("street_number") != -1) {
            address_value = address_value + ' ' + addr_data;
        }else if(arr_types.indexOf("route") != -1) {
            address_value = address_value + ' ' + addr_data;
        }else if(arr_types.indexOf("postal_code") != -1) {
            postcode_value = addr_data;
        }else if(arr_types.indexOf("political") != -1) {

            if( (district_value=='') && ((arr_types.indexOf("sublocality_level_2") != -1) || (arr_types.indexOf("sublocality_level_1") != -1) || (arr_types.indexOf("administrative_area_level_3") != -1) || (arr_types.indexOf("locality") != -1)) ) {
                addr_data = addr_data.replace("แขวง", "");
                addr_data = addr_data.replace("ตำบล", "");
                addr_data = addr_data.replace("Khwaeng ", "");
                addr_data = addr_data.replace("Tambon ", "");
                district_value = addr_data;
            }else if( (arr_types.indexOf("sublocality_level_1") != -1) || (arr_types.indexOf("administrative_area_level_2") != -1) ) {
                addr_data = addr_data.replace("เขต", "");
                addr_data = addr_data.replace("อำเภอ", "");
                addr_data = addr_data.replace("Khet ", "");
                addr_data = addr_data.replace("Amphoe ", "");
                amphure_value = addr_data;
            }else if( (arr_types.indexOf("administrative_area_level_1") != -1) || (arr_types.indexOf("locality") != -1) ) {
                addr_data = addr_data.replace("Krung Thep Maha Nakhon", "Bangkok");
                addr_data = addr_data.replace("Chang Wat", "");
                province_value = addr_data;
            }

        }
        /* else if(arr_types.indexOf("country") != -1) {

        }*/
    });

    if (document.getElementById('modal_address') !=null) {
        $('#modal_address').val(address_value.trim());
    }
    if (document.getElementById('modal_district') !=null) {
        $('#modal_district').val(district_value.trim());
    }
    if (document.getElementById('modal_amphure') !=null) {
        $('#modal_amphure').val(amphure_value.trim());
    }
    if (document.getElementById('modal_province') !=null) {
        $('#modal_province').val(province_value.trim());
    }
    if (document.getElementById('modal_postcode') !=null) {
        $('#modal_postcode').val(postcode_value.trim());
    }
}

function modal_resetElementAddressComponents()
{
    if (document.getElementById('modal_address') !=null) {
        $('#modal_address').val('');
    }
    if (document.getElementById('modal_district') !=null) {
        $('#modal_district').val('');
    }
    if (document.getElementById('modal_amphure') !=null) {
        $('#modal_amphure').val('');
    }
    if (document.getElementById('modal_province') !=null) {
        $('#modal_province').val('');
    }
    if (document.getElementById('modal_postcode') !=null) {
        $('#modal_postcode').val('');
    }
}


//set map from geolocation
function modal_initGeocodeMapWithCurrentLocation(){
    if ("geolocation" in navigator){
        // geolocation is available
        if (navigator.geolocation){
            navigator.geolocation.getCurrentPosition(
                modal_displayLocationInfo, modal_handleLocationError
            );
        }else{
            // Geolocation is not supported by this browser
            modal_tryIPLookupGeolocation();
        }
    }
}
function modal_displayLocationInfo(position) {
    var lat = position.coords.latitude;
    var lng = position.coords.longitude;
    modal_setGoogleMap(lat, lng, 1);
}
function modal_handleLocationError(error) {
    console.log('error.code: '+error.code+' '+error.message);
    modal_tryIPLookupGeolocation();
}
function modal_tryIPLookupGeolocation() {
    $.getJSON('https://ipinfo.io/geo', function(response) {
        loc = response.loc.split(',');
        modal_setGoogleMap(loc[0], loc[1], 1);
    });
}
