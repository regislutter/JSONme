<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test GMaps</title>

    <style>
        html, body { width: 100%; height: 100%; }
        #gmap { width: 100%; height: 100%; }
    </style>

</head>
<body>

<div id="gmap" class="scrollable"></div>

<script src="assets/js/libs/jquery.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAD8b-WDnJUoSX4sBO0BjpTI_zqC2KC1qY">//&signed_in=true&callback=initMap</script>
<script type="text/javascript">
    $(document).ready(function(){
        var _gMap = document.getElementById('gmap');

        setTimeout(function(){
            var map = new google.maps.Map(_gMap, {
                center: {lat: -34.397, lng: 150.644},
                zoom: 10,
                draggable : true
            });

            var infoWindow = new google.maps.InfoWindow({map: map});

            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    infoWindow.setPosition(pos);
                    infoWindow.setContent('Your location.');
                    map.setCenter(pos);
                }, function() {
                    infoWindow.setPosition(map.getCenter());
                    infoWindow.setContent('Error: The Geolocation service failed.');
                });
            } else {
                // Browser doesn't support Geolocation
                infoWindow.setPosition(map.getCenter());
                infoWindow.setContent('Error: Your browser doesn\'t support geolocation.');
            }

            // Load map data
//            map.data.loadGeoJson('./json/map.json');
        }, 1000);

    });
</script>
</body>
</html>