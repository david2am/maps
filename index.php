<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Librería JQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Versión compilada y comprimida del CSS de Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <title>Coffe time</title>
    <style>
        #map {
            width: 100%;
            height: 400px;
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
    <!-- Servicio de Geolocalización y Places -->
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

            // Mostrar los marcadores al hacer clic en boton
            function showListings () {
                // Deteccion de ubicacion actual
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var pos = { lat: position.coords.latitude,
                                    lng: position.coords.longitude };
                        infoWindow.setPosition(pos);
                        infoWindow.setContent('Mi ubicación.');
                        map.setCenter(pos);
                        console.log(position);
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
            // Deteccion de evento click
            document.getElementById('show-listings').addEventListener('click', showListings); 
        }

        // Manejo de errores de localizacion
        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ?
                                'Error: El servicio de Geolocalización falló.' :
                                'Error: Su buscador no soporta geolocalización.');
        }
    </script>

    <style>
        th, td {
            text-align: center;
        }
    </style>
     <!-- Servicio de OpenWeatherMap -->
    <script>
        var values;
        var t;
        
        $.ajax({
            type: "GET",
            url: "http://api.openweathermap.org/data/2.5/weather?q=Medellín,CO PR&APPID=24c141d95153e5c76b8a53ccb5db9868",
            dataType: "json",
            success: function (data) {
                values = data.main;
                t = values.temp - 273.15;
                h = values.humidity;
                d = data.weather.description;
                $('#clima').html('<table>' +
                                    '<tr>' +
                                        '<th>Temperatura</th>' +
                                        '<th>Humedad</th>' +
                                        '<th>Descripción</th>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td>' + t + '°C</td>' +
                                        '<td>' + h + '%</td>' +
                                        '<td>' + d + '</td>' +
                                    '</tr>' + 
                                    '</table>');
                console.log(t);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
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
    <input type="button" value="Mi localización" id="show-listings">
    <div id="clima"></div>
</body>
</html>