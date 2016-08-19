<?php
/**
 * Author: MurDaD
 * Author URL: https://github.com/MurDaD
 *
 * Description: Config file, must be included in every script
 */

use includes\Settings;

date_default_timezone_set('Europe/Kiev');
define('DEBUG', true);

if(DEBUG) {
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set("display_errors", 1);
}

/** Check if vendor folder exists
 *  De-comment if composer is used
 */
/*if(!file_exists(dirname(__FILE__).'/vendor/autoload.php')) die ('Please, run \'composer install\' from root dir');
include dirname(__FILE__).'/vendor/autoload.php';*/

/**
 * Include helper functions
 */
if(file_exists(dirname(__FILE__).'/includes/Helpers.php'))
    include dirname(__FILE__).'/includes/Helpers.php';

/**
 * Lazy load of app classes
 */
spl_autoload_register(function($class) {
    $map = [
        'app\API' => dirname(__FILE__).'/app/API.php',
        'app\Server' => dirname(__FILE__).'/app/Server.php',
        'includes\Exception' => dirname(__FILE__).'/includes/Exception.php',
        'includes\Settings' => dirname(__FILE__).'/includes/Settings.php',
    ];
    if($map[$class]) {
        try {
            include $map[$class];
        } catch (Exception $e) {
            echo $e->getMessage() . '. Including ' . $class . ' from ' . $map[$class];
        }
    }
});

/**
 * Default settings
 * Config DB here if needed db_host, db_user, db_pass, db_database
 */
Settings::set('datetime_format','Y-m-d H:i:s');
/**
 * http://www.rocketroute.com/ API access data
 */
Settings::set('login','max_shakh@yahoo.com');
Settings::set('pass','qwert123');
Settings::set('api','api.rocketroute.com');
Settings::set('auth','fly.rocketroute.com');
Settings::set('md5','Iw8DfRlZfPqHbW3bocNJ');
