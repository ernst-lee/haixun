<?php
/**
 * This file is part of the wengo/basesdk.
 *
 * (c) basesdk <398711943@qq.com>
 *
 */

namespace Haixun\DownloaderMiddlewares;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MetaMiddleware
{
    public function processResponse() {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                $promise = $handler($request->withoutHeader('meta'), $options);
                return $promise->then(
                    function (ResponseInterface $response) use ($request, $options) {
                        if(!empty($request->getHeader('meta'))) {
                            return $response->withHeader('X-GUZZLE-META', $request->getHeader('meta'));
                        }
                        return $response;
                    }
                );
            };
        };
    }
}