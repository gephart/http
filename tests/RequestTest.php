<?php

require_once __DIR__ . '/../vendor/autoload.php';

class RequestTest extends \PHPUnit\Framework\TestCase
{

    public function testFactory()
    {
        $this->setSuperglobals();

        $requestFactory = new \Gephart\Http\RequestFactory();
        $request = $requestFactory->createFromGlobals()
            ->withHeader("Authorize", "Token: 123")
            ->withAttribute("test", "atribute");

        $this->assertEquals("/index.html", $request->getRequestTarget());
        $this->assertEquals("1.0", $request->getProtocolVersion());
        $this->assertEquals(["test" => "post"], $request->getParsedBody());
        $this->assertEquals(["test" => "get"], $request->getQueryParams());
        $this->assertEquals(["test" => "cookie"], $request->getCookieParams());
        $this->assertEquals("GET", $request->getMethod());
        $this->assertEquals("atribute", $request->getAttribute("test"));
        $this->assertEquals(["test" => "atribute"], $request->getAttributes());
        $this->assertEquals("Token: 123", $request->getHeaderLine("Authorize"));

        $request = $request
            ->withoutHeader("Authorize")
            ->withoutAttribute("test")
            ->withProtocolVersion("1.1")
            ->withMethod("POST")
            ->withCookieParams(["test" => "cookie2"])
            ->withQueryParams(["test" => "get2"])
            ->withParsedBody(["test" => "post2"]);

        $this->assertEquals("1.1", $request->getProtocolVersion());
        $this->assertEquals(["test" => "post2"], $request->getParsedBody());
        $this->assertEquals(["test" => "get2"], $request->getQueryParams());
        $this->assertEquals(["test" => "cookie2"], $request->getCookieParams());
        $this->assertEquals("POST", $request->getMethod());
        $this->assertEquals([], $request->getAttributes());
        $this->assertEquals("", $request->getHeaderLine("Authorize"));
    }

    public function setSuperglobals()
    {
        $_GET = ["test" => "get"];
        $_POST = ["test" => "post"];
        $_COOKIE = ["test" => "cookie"];
        $_SERVER['SERVER_PROTOCOL'] = "HTTP/1.0";
        $_SERVER['SERVER_PORT'] = "80";
        $_SERVER['SERVER_NAME'] = "www.gephart.cz";
        $_SERVER['REQUEST_URI'] = "/index.html";
        $_SERVER['REQUEST_METHOD'] = "GET";
    }

}