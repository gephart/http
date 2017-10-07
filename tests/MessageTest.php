<?php

use Gephart\Http\Exception\MessageException;
use Gephart\Http\Message;

require_once __DIR__ . '/../vendor/autoload.php';

class MessageTest extends \PHPUnit\Framework\TestCase
{
    public function testHeaders()
    {
        $message = (new Message())
            ->withHeader("content-type", ["text/html", "text/xml"])
            ->withHeader("authorize", "token: 123");

        $headerLine = $message->getHeaderLine("Content-Type");
        $this->assertEquals($headerLine, "text/html,text/xml");

        $header = $message->getHeader("Content-Type");
        $this->assertEquals($header, ["text/html", "text/xml"]);

        $this->assertTrue($message->hasHeader("Content-Type"));
        $message = $message->withoutHeader("Content-Type");
        $this->assertFalse($message->hasHeader("Content-Type"));

        $message = $message->withAddedHeader("Authorize", "token: 321");
        $header = $message->getHeader("Authorize");
        $this->assertEquals($header, ["token: 123", "token: 321"]);
    }

    public function testProtocol()
    {
        $message = new Message();
        $message_10 = $message->withProtocolVersion("1.0");

        $this->assertEquals($message->getProtocolVersion(), "1.1");
        $this->assertEquals($message_10->getProtocolVersion(), "1.0");

        $is_exception = false;
        try {
            $message->withProtocolVersion("0.9");
        } catch (MessageException $exception) {
            $is_exception = true;
        }
        $this->assertTrue($is_exception);
    }

    public function bodyTest()
    {
        $body = new \Gephart\Http\Stream("php://memory", "w");
        $body->write("Test");

        $message = new Message();
        $message = $message->withBody($body);
        $body = $message->getBody();

        $body->write("Test2");
        $body->rewind();

        $this->assertEquals($body->getContents(), "TestTest2");
    }
}