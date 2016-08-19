<?php
/**
 * Author: MurDaD
 * Author URL: https://github.com/MurDaD
 *
 * Description: Home page with form and Google Map
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style type="text/css">
        html, body { height: 100%; }
        #map { height: 100%; min-height: 500px}
    </style>
    <script type="text/javascript" src="//code.jquery.com/jquery-1.12.3.js"></script>
    <script type="text/javascript" src="./js/script.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAa7iVqTx-TuSlOqs77fe1qrakKkLm9VN0&callback=initMap">
    </script>
</head>
<body>
<form id="getNOTAM">
    <input type="text" name="icao" />
    <input type="submit" />
</form>
<br/><br/>
<div id="map" width="100%"></div>
</body>
</html>

