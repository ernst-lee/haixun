<?php
namespace Haixun\DownloaderMiddlewares;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class EffectiveUrlMiddleware
{
    public function processResponse() {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                $promise = $handler($request, $options);
                return $promise->then(
                    function (ResponseInterface $response) use ($request, $options) {
                        return $response->withHeader('X-GUZZLE-EFFECTIVE-URL', $request->getUri()->__toString());
                    }
                );
            };
        };
    }
}