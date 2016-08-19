<?php
/**
 * Author: MurDaD
 * Author URL: https://github.com/MurDaD
 *
 * Description: API class
 */

namespace app;

class Server extends API
{
    /**
     * Server constructor.
     * @param $user_login
     * @param $user_pass
     * @param $api_domain
     * @param $auth_domain
     * @param $md5
     */
    function __construct($user_login, $user_pass, $api_domain, $auth_domain, $md5)
    {
        parent::__construct($user_login, $user_pass, $api_domain, $auth_domain, $md5);
    }

    /**
     * Get NOTAM FROM ICAO with converting to JSON
     *
     * @param $ICAO
     * @return string
     */
    public function getNOTAM($ICAO)
    {
        return $this->toJson(parent::getNOTAM($ICAO));
    }

    /**
     * Converts array to JSON
     *
     * @param $data
     * @return string
     */
    public function toJson($data)
    {
        return json_encode($data);
    }
}