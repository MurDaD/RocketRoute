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
    function __construct($user_login, $user_pass, $api_domain, $md5)
    {
        $this->user_login = $user_login;
        $this->user_pass = md5($user_pass);
        $this->api_domain = $api_domain;
        $this->md5 = $md5;
        // Init curl
        $this->ch = curl_init();
        // Authenticate
        $this->auth();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        //curl_close($this->ch);
    }

    /**
     * Authenticate user.
     * Saves access key to local var $key
     */
    private function auth()
    {
        $this->construct_url('/remote/auth', true);
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
        logD($result);
    }

    /**
     * Creates API url from domain and path
     *
     * @param $path
     * @param bool $https
     */
    private function construct_url($path, $https = false)
    {
        $this->url = ($https ? 'https' : 'http') . '://' . $this->api_domain . $path;
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
                if(count($data) == 1 && is_string($root)) {
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
    private function array_to_xml($array, &$xml) {
        foreach($array as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xml->addChild("$key");
                    array_to_xml($value, $subnode);
                } else {
                    array_to_xml($value, $xml);
                }
            } else {
                $xml->addChild("$key","$value");
            }
        }
    }

    /**
     * Decodes result XML to array
     *
     * @param $result
     * @param string $format
     * @return mixed
     */
    private function decode($result, $format = 'xml')
    {
        if($format == 'xml') {
            $p = xml_parser_create();
            xml_parse_into_struct($p, $result, $vals, $index);
            xml_parser_free($p);
            return $vals;
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
    private function request($url, $method = 'POST', $postfields = array())
    {
        logD($url);
        logD($postfields);
        curl_setopt_array($this->ch, array(
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => ($method == 'POST'),
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_URL => $url
        ));
        return curl_exec($this->ch);
    }
}