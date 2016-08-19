var map;                                                        // Google map
var markerIcon = 'images/icon.png';                        // Map marker different icon
var minZoomLevel = 15;                                          // Minimal zoom level

$(document).ready(function(){
    $("#getNOTAM").on("submit", function(e){
        e.preventDefault();
        APIgetNOTAM($(this).find("input[type=text]").val());
    });
});

/**
 * Get NOTAM and draw it on Google map using local API
 *
 * @param icao
 * @constructor
 */
function APIgetNOTAM(icao) {
    if(!icao.trim()) {
        alert('ICAO is empty! Please, fill it.')
    } else if(!/^[a-zA-Z]+$/.test(icao) || icao.length != 4) {
        alert('ICAO must contain only 4 letter!')
    } else {
        $.ajax({
            url: "api.php",
            method: 'GET',
            data: {
                icao: icao
            },
            dataType: 'json'
        }).done(function(data) {
            if(data.length > 0) {
                // center of the first result
                // var center = {lat: parseFloat(data[0].lat),lng:parseFloat(data[0].lng)};
                var bounds = new google.maps.LatLngBounds();

                /**
                 * setting center to the first resul
                 * deprecated because using bounds
                 */
                //map.setCenter(center);
                $.each( data, function( index, value ){
                    if(parseFloat(value.lat) !== null && parseFloat(value.lng) !== null) {
                        // Position of the marker
                        var position = {lat: parseFloat(value.lat), lng: parseFloat(value.lng)};
                        // Onclick box of the marker
                        var contentString = '<div id="content">' +
                            '<div id="siteNotice">' +
                            '</div>' +
                            '<h1 id="firstHeading" class="firstHeading">' + value.id + '</h1>' +
                            '<div id="bodyContent">' +
                            '<p>' + value.ItemE + '</p>' +
                            '</div>' +
                            '</div>';
                        var infowindow = new google.maps.InfoWindow({
                            content: contentString
                        });
                        var marker = new google.maps.Marker({
                            position: position,
                            map: map,
                            title: value.id,
                            icon: markerIcon,
                            // This marker is 32 pixels wide by 29 pixels high.
                            size: new google.maps.Size(32, 29),
                            // The origin for this image is (0, 0).
                            origin: new google.maps.Point(0, 0),
                            // The anchor for this image is the base of the flagpole at (16, 15).
                            anchor: new google.maps.Point(16, 15)
                        });
                        marker.addListener('click', function () {
                            infowindow.open(map, marker);
                        });
                        bounds.extend(marker.getPosition());
                    }
                });
                map.fitBounds(bounds);

            } else {
                alert('No NOTAM fount for this ICAO. Maybe, it\'s wrong?');
            }
        });
    }
}

/**
 * Initiate Google Map
 */
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: -34.397, lng: 150.644},
        zoom: 8
    });
    /**
     * If zoom level from bounds is very low, zoom out to see the map
     */
    google.maps.event.addListener(map, 'zoom_changed', function() {
        zoomChangeBoundsListener =
            google.maps.event.addListener(map, 'bounds_changed', function(event) {
                if (this.getZoom() > minZoomLevel) {
                    // Change max/min zoom here
                    this.setZoom(minZoomLevel);
                    this.initialZoom = false;
                }
                google.maps.event.removeListener(zoomChangeBoundsListener);
            });
    });
}