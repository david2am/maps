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