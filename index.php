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
        var infoWindow;

        function initMap() {
            var medellin = {lat: 6.25184, lng: -75.56359};

            map = new google.maps.Map(document.getElementById('map'), {
                center: medellin,
                zoom: 15
            });
            // Instanciar ventana de informacion de marcadores
            var infoWindow = new google.maps.InfoWindow({map: map});

            // Deteccion de ubicacion actual
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = { lat: position.coords.latitude,
                                lng: position.coords.longitude };
                    infoWindow.setPosition(pos);
                    infoWindow.setContent('Mi ubicación.');
                    map.setCenter(pos);

                    // Busqueda de cafeterias cercanas a nuestra ubicacion actual
                    var service = new google.maps.places.PlacesService(map);
                    service.nearbySearch({ location: pos,
                                           radius: 500,
                                           type: ['cafe'] }, 
                                           callback);
                },  
                function() {
                    handleLocationError(true, infoWindow, map.getCenter());
                });
            } else {
                // Browser no soporta Geolocalizacion
                handleLocationError(false, infoWindow, map.getCenter());
            }

            // Crear marcadores para las cafeterias cercanas
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
                    position: place.geometry.location,
                    animation: google.maps.Animation.DROP,
                });
                // Mostrar nombre de la cafeteria
                google.maps.event.addListener(marker, 'click', function() {
                    infoWindow.setContent(place.name);
                    infoWindow.open(map, this);
                });
            }   
        }

        // Manejo de errores de localizacion
        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ?
                                'Error: The Geolocation service failed.' :
                                'Error: Your browser doesn\'t support geolocation.');
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
    <input type="button" value="Mi localización" class="mi-localizacion">
</body>
</html>