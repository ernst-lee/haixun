<?php
namespace Haixun\DownloaderMiddlewares;


use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UserAgentMiddleware {
    public function processRequest(...$params) {
        return Middleware::mapRequest(function (RequestInterface $request) use ($params) {
            return $request->withHeader('User-Agent', \Campo\UserAgent::random());
        });
    }
//
//    public function processResponse(...$params) {
//        return Middleware::mapResponse(function (ResponseInterface $response) use ($params) {
//            return $response->withHeader('X-Foo1', );
//        });
//    }
}