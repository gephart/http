<?php

use Gephart\Http\Uri;

require_once __DIR__ . '/../vendor/autoload.php';

class UriTest extends \PHPUnit\Framework\TestCase
{

    public function testUri()
    {
        $url = "https://test:test.123@www.gephart.cz:447/download?petr=pan#main";

        $uri = new Uri($url);

        $this->assertEquals($uri->getAuthority(), "test:test.123@www.gephart.cz:447");
        $this->assertEquals($uri->getFragment(), "main");
        $this->assertEquals($uri->getHost(), "www.gephart.cz");
        $this->assertEquals($uri->getPath(), "/download");
        $this->assertEquals($uri->getPort(), "447");
        $this->assertEquals($uri->getQuery(), "petr=pan");
        $this->assertEquals($uri->getScheme(), "https");
        $this->assertEquals($uri->getUserInfo(), "test:test.123");
        $this->assertEquals((string)$uri, $url);
    }

}