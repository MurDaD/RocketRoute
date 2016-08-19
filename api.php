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
        \includes\Settings::get('login'),
        \includes\Settings::get('pass'),
        \includes\Settings::get('api'),
        \includes\Settings::get('auth'),
        \includes\Settings::get('md5')
    );
    echo $api->getNOTAM($_GET['icao']);
}