<?php

use Gephart\Http\Response;
use Gephart\Http\Stream;

require_once __DIR__ . '/../vendor/autoload.php';

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    private $response;

    public function setUp()
    {
        $body = new Stream("php://temp","rw");
        $body->write("Test response");

        $this->response = new Response($body, 403, ["Content-type" => "text/plain"]);
    }

    public function testBody()
    {
        $responseBody = $this->response->getBody();
        $responseBody->rewind();

        $this->assertEquals("Test response", $responseBody->getContents());
    }

    public function testStatus()
    {
        $response = $this->response;

        $this->assertEquals("Forbidden", $response->getReasonPhrase());
        $this->assertEquals("403", $response->getStatusCode());

        $response = $response->withStatus(200);

        $this->assertEquals("OK", $response->getReasonPhrase());
        $this->assertEquals("200", $response->getStatusCode());

        $response = $response->withStatus(200, "It's OK");

        $this->assertEquals("It's OK", $response->getReasonPhrase());
    }
}