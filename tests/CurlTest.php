<?php

use Mib\Component\Curl\Curl;

/**
 * Class TestableCurl
 * Helper class to test the curl class
 */
class TestableCurl extends Curl
{
    public $init = false;

    public $exec = false;

    public $option;

    public $value;

    protected function init()
    {
        $this->init = true;
    }

    protected function setOption($option, $value)
    {
        $this->option = $option;
        $this->value = $value;
    }

    protected function exec()
    {
        $this->exec = true;
    }
}

/**
 * Class CurlTest
 * UnitTests for class Curl
 */
class CurlTest extends PHPUnit_Framework_Testcase
{
    /**
     * @var TestableCurl
     */
    private $instance;

    public function setUp()
    {
        $this->instance = new TestableCurl();

        $this->assertEquals(true, $this->instance->init);
    }

    /**
     * @dataProvider invalidUrlProvider
     * @expectedException Mib\Component\Curl\Exception\InvalidUrlException
     * @param $url
     */
    public function testSetUrlInvalidUrlThrows($url)
    {
        $this->instance->setUrl($url);
    }

    /**
     * @dataProvider validUrlProvider
     * @param $url
     */
    public function testSetUrlValidUrlSetsOption($url)
    {
        $this->instance->setUrl($url);

        $this->assertEquals(CURLOPT_URL, $this->instance->option);

        $this->assertEquals($url, $this->instance->value);
    }

    /**
     * @dataProvider statusProvider
     * @param $status
     * @param $expected
     */
    public function testFollowRedirectsStatusSetsOption($status, $expected)
    {
        $this->instance->followRedirects($status);

        $this->assertEquals(CURLOPT_FOLLOWLOCATION, $this->instance->option);

        $this->assertEquals($expected, $this->instance->value);
    }

    /**
     * @dataProvider statusProvider
     * @param boolean $status
     * @param integer $expected
     */
    public function testReturnTransferStatusSetsOption($status, $expected)
    {
        $this->instance->returnTransfer($status);

        $this->assertEquals(CURLOPT_RETURNTRANSFER, $this->instance->option);

        $this->assertEquals($expected, $this->instance->value);
    }

    /**
     * @expectedException Mib\Component\Curl\Exception
     */
    public function testGetWithoutUrlSetThrows()
    {
        $this->instance->get();
    }

    /**
     * @dataProvider validUrlProvider
     * @param string $url
     */
    public function testGetWithUrlSetExecSession($url)
    {
        $this->instance
            ->setUrl($url)
            ->get();

        $this->assertEquals(true, $this->instance->exec);
    }

    /**
     * @dataProvider validUrlProvider
     * @param string $url
     */
    public function testGetWithUrlAndReturnTransferReturnsString($url)
    {
        $curl = new Curl();

        $responseString = $curl
            ->setUrl($url)
            ->returnTransfer()
            ->get();

        $this->assertInternalType('string', $responseString);
    }

    public function invalidUrlProvider()
    {
        return [
            [ null ],
            [ true ],
            [ false ],
            [ '' ],
            [ 'invalid-url' ],
        ];
    }

    public function validUrlProvider()
    {
        return [
            [ 'http://www.google.de' ],
            [ 'http://php.net' ],
        ];
    }

    public function statusProvider()
    {
        return [
            [ false, 0 ],
            [ true, 1  ]
        ];
    }
}
