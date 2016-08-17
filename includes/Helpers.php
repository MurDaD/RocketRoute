<?php
/**
 * Author: MurDaD
 * Author URL: https://github.com/MurDaD
 *
 * Description: Helper functions
 */

/**
 * Debug variable (string, array, object)
 *
 * @param $var
 */
function logD($var) {
    if(DEBUG) {
        if(is_array($var)) {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        } elseif(is_object($var)) {

        } else {
            var_dump($var);
        }
    }
}