<?php

namespace Gephart\Http;

class RequestFactory
{
    public function createFromGlobals()
    {
        $uri = new Uri($this->getUrl());
        $body = new Stream("php://input");
        $method = $_SERVER["REQUEST_METHOD"];
        $headers = $this->getHeaders();
        $uploadedFiles = $this->getUploadedFiles();
        $protocol = $this->getProtocolVersion();

        return new Request(
            $uri,
            $body,
            $_SERVER,
            $_GET,
            $_POST,
            $method,
            $_COOKIE,
            $headers,
            $uploadedFiles,
            $protocol
        );
    }

    private function getUrl()
    {
        $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $_SERVER['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
            ? $_SERVER['HTTP_X_FORWARDED_HOST']
            : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $_SERVER['SERVER_NAME'] . $port;

        return $protocol . '://' . $host . $_SERVER['REQUEST_URI'];
    }

    private function getHeaders()
    {
        if (function_exists("apache_request_headers")) {
            return apache_request_headers();
        }

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    private function getProtocolVersion()
    {
        if (empty($_SERVER["SERVER_PROTOCOL"])) {
            return "1.1";
        }

        return substr($_SERVER["SERVER_PROTOCOL"], 5, 3);
    }

    private function getUploadedFiles()
    {
        if (empty($_FILES)) {
            return [];
        }

        $files = [];
        foreach ($_FILES as $file) {
            if (isset($file["tmp_name"]) && is_string($file["tmp_name"])) {
                $files[] = $this->getUploadedFileByStd($file);
            } elseif (isset($file["tmp_name"]) && is_array($file["tmp_name"])) {
                $keys = array_keys($file["tmp_name"]);
                foreach ($keys as $key) {
                    $files[] = $this->getUploadedFileByStd([
                        "tmp_name" => $file["tmp_name"][$key],
                        "size" => $file["size"][$key],
                        "error" => $file["error"][$key],
                        "name" => $file["name"][$key],
                        "type" => $file["type"][$key]
                    ]);
                }
            }
        }
        return $files;
    }

    private function getUploadedFileByStd(array $file)
    {
        if (empty($file["tmp_name"])) {
            throw new \InvalidArgumentException("Property \$file must be standard array from \$_FILES");
        }

        return new UploadedFile(
            $file['tmp_name'],
            $file['size'],
            $file['error'],
            $file['name'],
            $file['type']
        );
    }
}
