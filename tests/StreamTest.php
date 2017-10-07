<?php

use Gephart\Http\Stream;

require_once __DIR__ . '/../vendor/autoload.php';

class StreamTest extends \PHPUnit\Framework\TestCase
{

    public function testNotReadable()
    {
        $stream = new Stream(__DIR__ . "/test.txt", "w");
        $this->assertFalse($stream->isReadable());
        $stream->close();
    }

    public function testReadable()
    {
        $stream = new Stream(__DIR__ . "/test.txt", "r");
        $this->assertTrue($stream->isReadable());
        $stream->close();
    }

    public function testWritable()
    {
        $stream = new Stream(__DIR__ . "/test.txt", "w");
        $this->assertTrue($stream->isWritable());
        $stream->close();
    }

    public function testNotWritable()
    {
        $stream = new Stream(__DIR__ . "/test.txt", "r");
        $this->assertFalse($stream->isWritable());
        $stream->close();
    }

    public function testStream()
    {
        $stream = new Stream("php://temp", "w+");
        $stream->write("Test stream");
        $stream->rewind();
        $content = $stream->getContents();
        $stream->close();

        $this->assertEquals($content, "Test stream");
    }

}