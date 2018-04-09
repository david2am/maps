<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
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
</body>
</html>