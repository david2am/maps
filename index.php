<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Versión compilada y comprimida del CSS de Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <title>Coffe time</title>
    <style>
        #map {
            width: 100%;
            height: 400px;
            background-color: grey;
        }
        html, body {
            height: 100%;
            font-family: Helvetica, 'Nexa Bold';
        }
        h2 {
            font-weight: bold;
            text-align: center;
        }
    </style>
    <script>

        var map;
        var infowindow;

        function initMap() {
            var medellin = {lat: 6.25184, lng: -75.56359};

            map = new google.maps.Map(document.getElementById('map'), {
                center: medellin,
                zoom: 15
            });

            infowindow = new google.maps.InfoWindow();
            var service = new google.maps.places.PlacesService(map);
            service.nearbySearch({
                location: medellin,
                radius: 500,
                type: ['cafe']
            }, callback);
        }

        function callback(results, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                for (var i = 0; i < results.length; i++) {
                    createMarker(results[i]);
                }
            }
        }

        function createMarker(place) {
            var placeLoc = place.geometry.location;
            var marker = new google.maps.Marker({
                map: map,
                position: place.geometry.location
            });

            google.maps.event.addListener(marker, 'click', function() {
                infowindow.setContent(place.name);
                infowindow.open(map, this);
            });
        }
        </script>
</head>
<body>
    <?php 
        // Insercion de la API key
        $f = fopen('credenciales.txt', 'r');
        $key = fgets($f);
        echo '<script src="https://maps.googleapis.com/maps/api/js?key=' . $key .'&libraries=places&callback=initMap" async defer></script>';
        fclose($f);
    ?>
    <h2>UN CAFÉ CERCA A TI</h2>
    <div id="map"></div>
</body>
</html>