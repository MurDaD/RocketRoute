<?php
/**
 * Testing app\API.php class
 */
use PHPUnit\Framework\TestCase;
use includes\Settings;

include '../config.php';

class ServerTest extends TestCase
{
    private $server;

    public function __construct()
    {
        $this->server = new \app\Server(
            Settings::get('login'),
            Settings::get('pass'),
            Settings::get('api'),
            Settings::get('auth'),
            Settings::get('md5')
        );
    }

    /**
     * test conversion to json
     */
    public function testJSON()
    {
        $this->assertEquals(
            '{"arr":{"one":1,"two":2,"three":3}}',
            $this->server->toJson(['arr' => ['one' => 1, 'two' => 2, 'three' => 3]])
        );
    }

    /**
     * Test NOTAM returning json
     * Errors are tested in APITest.php
     */
    public function testNOTAM()
    {
        $notamArr = [
            'ASDF' => '[]',
            'EGMC' => '[{"id":"C4114\/16","lat":51.566666666667,"lng":0.7,"ItemQ":"EGTT\/QLBAS\/V\/M \/A \/000\/999\/5134N00042E","ItemE":"AERODROME BEACON U\/S"},{"id":"C3904\/16","lat":51.566666666667,"lng":0.7,"ItemQ":"EGTT\/QMPLC\/IV\/BO \/A \/000\/999\/5134N00042E","ItemE":"STANDS 9, 19, 20 AND 21 U\/S"},{"id":"C3897\/16","lat":51.566666666667,"lng":0.7,"ItemQ":"EGTT\/QPAXX\/I\/NBO \/A \/000\/999\/5134N00042E","ItemE":"WEF 15 SEPT 2016 (AIRAC 10\/2016) UNTIL 13 OCT 2016 (AIRAC 11\/2016)\nAMEND SOUTHEND GEGMU 1B 1D STAR LEVEL PLANNING\nDELETE NIMRU - FL220\nINSERT LOGAN (+30NM) - FL220 30NM BEFORE LOGAN\nDELETE RUNUB - FL220\nINSERT LOGAN (+30NM) - FL220 30NM BEFORE LOGAN\nAD 2-EGMC-7-1 REFERS"},{"id":"C3828\/16","lat":51.566666666667,"lng":0.7,"ItemQ":"EGTT\/QNXAS\/V\/B \/A \/000\/999\/5134N00042E","ItemE":"VDF 130.775MHZ U\/S"},{"id":"C3287\/16","lat":51.566666666667,"lng":0.7,"ItemQ":"EGTT\/QOLAS\/IV\/M \/A \/000\/999\/5134N00042E","ItemE":"OBSTRUCTION LGT ON HANGAR U\/S. PSN 213M SOUTHEAST OF RUNWAY CL"}]',
            'EGMD' => '[{"id":"C4135\/16","lat":50.95,"lng":0.93333333333333,"ItemQ":"EGTT\/QMPLC\/IV\/BO \/A \/000\/999\/5057N00056E","ItemE":"APRON BRAVO STANDS 7 AND 8 WITHDRAWN FROM USE."},{"id":"C4134\/16","lat":50.95,"lng":0.93333333333333,"ItemQ":"EGTT\/QOBCE\/IV\/M \/AE \/000\/001\/5057N00056E","ItemE":"HANGAR CONSTRUCTION WIP POSITION 505729.25N 0005556.55E (LYDD\nAD) BEARING 295 DEG 540M FM ARP 40FT AGL\/50FT AMSL."},{"id":"C4133\/16","lat":50.95,"lng":0.93333333333333,"ItemQ":"EGTT\/QFAXX\/IV\/NBO \/A \/000\/999\/5057N00056E","ItemE":"ALL HELICOPTERS SHALL USE RWY 21\/03 FOR LANDING\/TAKE-OFF AS\nDIRECTED BY ATC. DIRECT JOIN TO APRON BRAVO NOT PERMITTED DUE\nTO HANGAR CONSTRUCTION WIP AT THE NW END OF APRON BRAVO."}]',
        ];
        foreach ($notamArr as $icao => $result) {
            $notam = $this->server->getNOTAM($icao);
            $this->assertEquals($result, $notam);
        }
    }
}