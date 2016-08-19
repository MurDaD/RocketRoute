<?php
/**
 * Testing app\API.php class
 */
use PHPUnit\Framework\TestCase;
use includes\Settings;

include '../config.php';

class APITest extends TestCase
{
    private $api;

    public function __construct()
    {
        $this->api = new \app\API(
            Settings::get('login'),
            Settings::get('pass'),
            Settings::get('api'),
            Settings::get('auth'),
            Settings::get('md5')
        );
    }

    /**
     * Testing if user can auth.
     * Also test if user login, password and key are correct
     */
    public function testAuth()
    {
        $this->api->auth();
        $this->assertInternalType('string', $this->api->getKey(), "Got a " . gettype($this->api->getKey()) . " instead of a string");
        $this->assertEquals('max_shakh@yahoo.com', $this->api->getUser());
    }

    /**
     * Testing NOTAM functionality
     */
    public function testNOTAM()
    {
        $notamArr = [
            'EGKA' => [
                'ItemE' => 'AD CLOSED',
                'ItemQ' => 'EGTT/QFALC/IV/NBO /A /000/999/5050N00018W',
                'id' => 'C3819/16',
                'lat' => '50.833333333333',
                'lng' => '-0.3'
            ]
        ];
        foreach ($notamArr as $icao => $n) {
            $notam = $this->api->getNOTAM($icao);
            $this->assertEquals($n, $notam[0]);
        }
    }

    /**
     * Testing Wrong NOTAM codes
     */
    public function testWrongNOTAM()
    {
        $this->assertEquals([], $this->api->getNOTAM('ASDF'));
        try{
            $this->api->getNOTAM('ASD');
            $this->fail("Expected exception 1162011 not thrown");
        }catch(\includes\Exception $e){
            $this->assertEquals("Wrong ICAO code.",$e->getMessage());
        }
    }

    /**
     * Test convertion NOTAM codes to coordinates
     */
    public function testNotamToCoords()
    {
        $coords = [
            '5050N00018W' => [
                50.833333333333,
                -0.3
            ],
            '5117N00048W' => [
                51.283333333333331,
                '-0.8'
            ]
        ];
        foreach ($coords as $n => $c) {
            $this->assertEquals($c, $this->api->toLongLat($n));
        }
    }
}