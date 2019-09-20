<?php
namespace Haixun\DownloaderMiddlewares;


use Closure;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Middleware;

class RetryAgentMiddleware {
    /**
     * 最大重试次数
     */
    const MAX_RETRIES = 10;

    /**
     * retryDecider
     * 返回一个匿名函数, 匿名函数若返回false 表示不重试，反之则表示继续重试
     * @return Closure
     */
    protected function decider()
    {
        return function (
            $retries,
            $request,
            $response = null,
            $exception = null
        ) {
            // 超过最大重试次数，不再重试
            if ($retries >= self::MAX_RETRIES) {
                return false;
            }

            // 请求失败，继续重试
            if ($exception instanceof ConnectException || $exception instanceof RequestException) {
                echo sprintf(' Retry Crawled <%s %s>', $request->getMethod(), $request->getUri()) . "\n";
                return true;
            } else if($exception != null) {
                var_dump(get_class($exception));
            }

            if ($response) {
                // 如果请求有响应，但是状态码大于等于500，继续重试(这里根据自己的业务而定)
                if ($response->getStatusCode() >= 500) {
                    return true;
                }
            }

            return false;
        };
    }

    /**
     * 返回一个匿名函数，该匿名函数返回下次重试的时间（毫秒）
     * @return Closure
     */
    protected function delay() {
        return function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        };
    }


    public function __invoke(callable $handler)
    {
        $decider = $this->decider();
        $delay = $this->delay();
        return Middleware::retry($decider, $delay)($handler);

    }
}