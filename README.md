Gephart HTTP
===

[![Build Status](https://travis-ci.org/gephart/http.svg?branch=master)](https://travis-ci.org/gephart/http)

Dependencies
---
 - PHP >= 7.1
 - psr/http-message = 1.0.1

Instalation
---

```
composer require gephart/http dev-master
```

Using
---

Request:

```php
$request = (new Gephart\Http\RequestFactory())->createFromGlobals();
```

Response:

```php
<?php

use Gephart\Http\Response;
use Gephart\Http\Stream;

class JsonResponseFactory
{
    public function createResponse($content, int $statusCode = 200, $headers = [])
    {
        $body = json_encode($content);

        $stream = new Stream("php://temp", "rw");
        $stream->write($body);

        $response = new Response($stream, $statusCode, $headers);
        return $response;
    }
}

$reponse = (new JsonResponseFactory)->createResponse(["data"=>"data"]);

```
