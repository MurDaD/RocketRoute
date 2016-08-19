<?php
/**
 * Author: MurDaD
 * Author URL: https://github.com/MurDaD
 *
 * Description: Ajax local API
 * Just a regular GET request in case only one functionality is required
 */
include 'config.php';

if($_GET['icao'] && is_string($_GET['icao'])) {
    $icao = addslashes(htmlspecialchars($_GET['icao']));

    $api = new \app\Server(
        'max_shakh@yahoo.com',          // your email
        'qwert123',                     // your password
        'api.rocketroute.com',          // api domain
        'fly.rocketroute.com',          // auth domain
        'Iw8DfRlZfPqHbW3bocNJ'          // md5
    );
    echo $api->getNOTAM($_GET['icao']);
}