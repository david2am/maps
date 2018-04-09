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
            height: 380px;
        }
        html, body {
            height: 100%;
            font-family: Helvetica, 'Nexa Bold';
        }
        h2 {
            text-align: center;
            font-weight: bold;
        }
        h3 {
            font-weight: bold;
            
        }
        th, td {
            text-align: center;
            width: 7em;
            font-weight: bold;
            color: black;
            font-size: medium;
        }
        #encabezado {
            background-color: #cd3301;
            color: black;
        }
        button {
            right: 40px;
            background-color: #cd3301;
            color: white;
            outline: none;
            border: 0;
            font-size: x-large;
            margin-right: 0.62em;
            padding: 0.3em;
        }
        #localidad {
            margin-left: 3em;
        }
        table {
            margin-left: 2em;
        }
        #presentacion_clima {
            margin-left: 0.6em;
        }
    </style>
    
    <script>
        // <<Servicio de Geolocallizacion y Places>>

        var map;
        var infoWindow;
        
        var barrio_long_name;
        var city_long_name;
        var country_long_name;

        function initMap() {
            var medellin = {lat: 6.25184, lng: -75.56359};
            geocoder = new google.maps.Geocoder();

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

                        // Busqueda de cafeterias cercanas a nuestra ubicacion actual
                        var service = new google.maps.places.PlacesService(map);
                        service.nearbySearch({ location: pos,
                                               radius: 500,
                                               type: ['cafe'] }, 
                                               callback);
                        
                        // Extraer las localidades ciudad-pais
                        var latlng = new google.maps.LatLng(pos.lat, pos.lng);
                        geocoder.geocode( { 'location': latlng}, function(results, status) {
                            if (status == 'OK') {
                                var country_long_name = '';
                                var city_long_name = '';
                                
                                // Extraer la localidad (ciudad-pais)
                                console.log(results[0].address_components);
                                results[0].address_components.map(address_component => {
                                    if (address_component.types[0] == 'country') {
                                        country_long_name = address_component.long_name;
                                    } else if (address_component.types[0] == 'administrative_area_level_2') {
                                        city_long_name = address_component.long_name;    
                                    } else if (address_component.types[0] == 'neighborhood') {
                                        barrio_long_name = address_component.long_name;
                                    }
                                });

                                // <<Servicio de OpenWeatherMap>>
                                
                                // Extraccion de la clave
                                var key = $("#OWM_key").html();
                                var values;
                                var t;
                                // Consulta al servicio
                                $.ajax({
                                    type: "GET",
                                    url: "http://api.openweathermap.org/data/2.5/weather?q="+ 
                                        city_long_name +","+ country_long_name +" PR&APPID=" + key,
                                    dataType: "json",
                                    success: function (data) {
                                        values = data.main;
                                        t = values.temp - 273.15;
                                        h = values.humidity;
                                        d = data.weather.description;
                                        // Se crea la tabla informativa del clima
                                        $('#clima').html('<table>' +
                                                            '<tr id="encabezado">' +
                                                                '<th>Temperatura</th>' +
                                                                '<th>Humedad</th>' +
                                                                '<th>Descripción</th>' +
                                                            '</tr>' +
                                                            '<tr>' +
                                                                '<td>' + Math.round(t) + '°C</td>' +
                                                                '<td>' + h + '%</td>' +
                                                                '<td>' + d + '</td>' +
                                                            '</tr>' + 
                                                            '</table>');
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        alert(errorThrown);
                                    }
                                });
                                
                                $('#localidad').html('<h3>' + barrio_long_name + 
                                                     ', '  + city_long_name + 
                                                     ', '  + country_long_name + '</h3>');
                                map.setCenter(results[0].geometry.location);
                                var marker = new google.maps.Marker({
                                    map: map,
                                    position: results[0].geometry.location
                                });
                            } else {
                                alert('Geocode was not successful for the following reason: ' + status);
                            }
                        
                        });
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
</head>
<body>
    <?php 
        // Lectura de las credenciales o API keys
        $f = fopen('credenciales.txt', 'r');
        $keys = [];
        while(!feof($f)) { 
            $keys[] = fgets($f);            
        }
        // Insercion de credenciales para su uso
        echo '<script src="https://maps.googleapis.com/maps/api/js?key=' . $keys[0] .'&libraries=places&callback=initMap" async defer></script>';
        echo '<p id="OWM_key" hidden>' . $keys[1] . '</p>';
        fclose($f);
    ?>

    <h2>UN CAFÉ CERCA A TI</h2>
    
    <div class="container">
        <div id="map"></div>

        <div class="row">
            <div id="localidad"></div>
            <button id="show-listings" class="pull-right">Mi localización</button>
        </div>

        <div class="row">
            <h3 id="presentacion_clima">Clima:</h3>
        </div>

        <div id="clima"></div>

    </div>
    

</body>
</html>