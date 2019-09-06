<?php

namespace Haixun\DownloaderMiddlewares;

use Closure;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * A class to log HTTP Requests and Responses of Guzzle.
 *
 * @author George Mponos <gmponos@gmail.com>
 */
class LoggerMiddleware
{
    /**
     * Called when the middleware is handled by the client.
     *
     * @param callable $handler
     * @return Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {

            $options['on_stats'] = function (TransferStats $stats) {
                echo sprintf('[%s] Crawled (%s) <%s %s>', date("Y-m-d H:i:s"), $stats->getResponse()->getStatusCode(), $stats->getRequest()->getMethod(), $stats->getEffectiveUri()) . "\n";
            };

            return $handler($request, $options)->then(
                $this->handleSuccess($request, $options),
                $this->handleFailure($request, $options)
            );
        };
    }

    /**
     * Returns a function which is handled when a request was successful.
     *
     * @param RequestInterface $request
     * @param array $options
     * @return Closure
     */
    private function handleSuccess(RequestInterface $request, array $options)
    {
        return function (ResponseInterface $response) use ($request, $options) {
            // On exception only is true then it must not log the response since it was successful.
            return $response;
        };
    }

    /**
     * Returns a function which is handled when a request was rejected.
     *
     * @param RequestInterface $request
     * @param array $options
     * @return Closure
     */
    private function handleFailure(RequestInterface $request, array $options)
    {
        return function (\Exception $reason) use ($request, $options) {
            return \GuzzleHttp\Promise\rejection_for($reason);
        };
    }
}
