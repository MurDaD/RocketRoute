<?php
/**
 * Author: MurDaD
 * Author URL: https://github.com/MurDaD
 *
 * Description: API class
 */

namespace app;

use includes\Exception;

class API
{
    private $user_login;        // User login (email)
    private $user_pass;         // User password
    private $api_domain;        // Domain for the API (dev or production)
    private $auth_domain;       // Domain for authentication (different from API)
    private $url;               // API url
    private $md5;               // MD5 code to access URL
    private $ch;                // Curl resource
    private $key;               // API access key
    private $key_expires;       // Time when API access key expires

    /**
     * API constructor.
     * @param $user_login
     * @param $user_pass
     * @param $api_domain
     * @param $md5
     */
    function __construct($user_login, $user_pass, $api_domain, $auth_domain, $md5)
    {
        $this->user_login = $user_login;
        $this->user_pass = md5($user_pass);
        $this->api_domain = $api_domain;
        $this->auth_domain = $auth_domain;
        $this->md5 = $md5;
        // Init curl
        $this->ch = curl_init();
        // Authenticate
         $this->auth();                   // Tested to be working
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        curl_close($this->ch);
    }

    /**
     * Authenticate user.
     * Saves access key to local var $key
     */
    public function auth()
    {
        $this->construct_url('/remote/auth', true, $this->auth_domain);
        $request = $this->encode([
            'AUTH' => [
                'USR' => $this->user_login,
                'PASSWD' => $this->user_pass,
                'DEVICEID' => '1299f2aa8935b9ffabcd4a2cbcd16b8d45691629',
                'PCATEGORY' => 'RocketRoute',
                'APPMD5' => $this->md5,
            ]
        ]);
        $result = $this->request($this->url, 'POST', $request);
        $this->user_login = $result->EMAIL->__toString();
        $this->key = $result->KEY->__toString();
        $this->key_expires = $result->EXPDATE->__toString();
    }

    /**
     * Returns user auth key
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Returns user login (email)
     * @return mixed
     */
    public function getUser()
    {
        return $this->user_login;
    }

    /**
     * Get NOTAM from API
     *
     * @param $ICAO
     * @return array
     */
    public function getNOTAM($ICAO)
    {
        if(preg_match('/^[A-Za-z0-9]+$/',$ICAO) && strlen($ICAO) == 4) {
            $result = [];
            $this->construct_url('/notam/v1/service.wsdl', true);
            $request = $this->encode([
                'REQNOTAM' => [
                    'USR' => $this->user_login,
                    'PASSWD' => $this->user_pass,
                    'ICAO' => $ICAO,
                ]
            ]);
            $res = $this->requestStatic($this->url, $request);
            foreach ($res->NOTAMSET->NOTAM as $notam) {
                $coords = $this->toLongLat($notam->ItemQ->__toString());
                if(is_array($coords)) {
                    array_push($result, [
                        'id' => $notam['id']->__toString(),
                        'lat' => $coords[0],
                        'lng' => $coords[1],
                        'ItemQ' => $notam->ItemQ->__toString(),
                        'ItemE' => $notam->ItemE->__toString(),
                    ]);
                } else {
                    die(new Exception('Could not convert coordinates'));
                }
            }
            return $result;
        } else {
            die(new Exception('Wrong ICAO code.'));
        }
    }

    /**
     * Parses ItemQ to find Google coordinates
     *
     * @param $str
     * @return mixed
     */
    private function toLongLat($str)
    {
        $coords = explode('/', $str);
        if(is_array($coords) && !empty(end($coords))) {
            $coords = $this->DMStoDEC(end($coords));
        }
        return [$coords['latitude'] , $coords['longitude']];
    }

    /**
     * Converts DMS from NOTAM to latitude, longitude
     *
     * @param $geoLocation
     * @return array
     */
    function DMStoDEC($geoLocation)
    {
        $returnArray = [];
        $originalGeoLocation = $geoLocation;
        if (!empty($geoLocation)) {
            $degree1 = mb_substr($geoLocation , 0 , 2);
            $hour1 = mb_substr($geoLocation , 2 , 2 );
            $geoLocation = substr( $geoLocation, 4 );
            if ( is_numeric( mb_substr( $geoLocation, - 3 ) ) ) {
                $geoLocation = substr( $geoLocation, 0, - 3 );
            }
            $hour2 = substr(substr( $geoLocation, - 3 ), 0 , -1);
            $geoLocation = substr(substr( $geoLocation, 0, - 3 ), 1 , 3);
            $degree2 = $geoLocation;
            //Equate Longitude and Latitude
            $latitude = $degree1 + $hour1 / 60;
            $longitude = $degree2 + $hour2 / 60;
            $longitude = strstr($originalGeoLocation, 'W')? '-'.$longitude : $longitude;
            $latitude =  strstr($originalGeoLocation, 'S')? '-'.$latitude : $latitude;
            $returnArray = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location' => $originalGeoLocation,
            ];
            return $returnArray;
        } else {
            die(new Exception('Wrong DMS code.'));
        }
    }


    /**
     * Creates API url from domain and path
     *
     * @param $path
     * @param bool $https
     * @param string $domain
     */
    private function construct_url($path, $https = false, $domain = '')
    {
        $this->url = ($https ? 'https' : 'http') . '://' . ($domain ? $domain : $this->api_domain) . $path;
    }

    /**
     * Encodes array to API request array
     *
     * @param array $data
     * @param string $format
     * @return array
     */
    private function encode($data = array(), $format = 'xml')
    {
        switch ($format) {
            case 'xml':
                $root = key($data);
                if (count($data) == 1 && is_string($root)) {
                    $xml = new \SimpleXMLElement('<' . key($data) . '/>');
                    $this->array_to_xml($data[key($data)], $xml);
                    $result = $xml->asXML();
                } else {
                    new Exception('Wrong XML array format. Must be 1 root element with data array inside.');
                }
                break;
            case 'json':
                $result = json_encode($data);
                break;
            default:
                new Exception('Can\'t encode to this format');
                break;
        }
        return ['req' => $result];
    }

    /**
     * Converts array to XML
     *
     * @param $array
     * @param $xml
     */
    private function array_to_xml($array, &$xml)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild("$key");
                    array_to_xml($value, $subnode);
                } else {
                    array_to_xml($value, $xml);
                }
            } else {
                $xml->addChild("$key", "$value");
            }
        }
    }

    /**
     * Decodes result XML to array
     *
     * @param $data
     * @param string $format
     * @return mixed
     */
    private function decode($data, $format = 'xml')
    {
        if ($format == 'xml') {
            $xml = simplexml_load_string($data);
            // Check if error included
            $this->error($xml);
            return $xml;
        } else {
            new Exception('Result format can\'t be decoded');
        }
    }

    /**
     * Executes request on link
     *
     * @param   string $url
     * @param   string $method
     * @param   array $postfields
     * @return  string
     */
    public function request($url, $method = 'POST', $postfields = array())
    {
        curl_setopt_array($this->ch, array(
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => ($method == 'POST'),
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_URL => $url
        ));
        return $this->decode(curl_exec($this->ch));
    }

    public function requestStatic($url, $request)
    {
        $client = new \SoapClient($url);
        $response = $this->decode($client->getNotam($request['req']));
        return $response;
    }

    private function error($data)
    {
        if(is_object($data->RESULT)) {
            $result = (string)$data->RESULT;
            if(is_numeric($result) && $result > 0) {
                $this->showApiError($data);
            }
            if ($result == 'ERROR') {
                $this->showApiError($data);
            }
        } else {
            die(new Exception('API returned NULL result'));
        }
    }

    private function showApiError($data)
    {
        if(is_object($data->MESSAGE)) {
            die(new Exception('API Error: ' . (string)$data->MESSAGE));
        } elseif(is_object($data->MESSAGES)) {
            die(new Exception('API Error: ' . (string)$data->MESSAGES->MSG));
        } else {
            die(new Exception('API Error!'));
        }
    }
}