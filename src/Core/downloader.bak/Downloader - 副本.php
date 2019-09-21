<?php
namespace Haixun\Core\Downloader;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Haixun\Http\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Request;


/**
 * 处理下载
 */
class Downloader {
    public $downloaderMiddlewareManager;

    /**
     * crawler
     * @var array
     */
    protected $crawler;

    /**
     * 中间件
     * @var array
     */
    protected $middlewares = [];

    /**
     * 处理者堆栈
     * @var \GuzzleHttp\HandlerStack
     */
    protected $handlerStack;

    public function __construct($crawler) {
        $this->crawler = $crawler;
        $this->downloaderMiddlewareManager = new DownloaderMiddlewareManager($this->crawler->settings->getDefaultSettings()['DOWNLOAD_MIDDLEWARES']);
//        $this->initBaseMiddleware();
    }

//    public function initBaseMiddleware() {
//        $downloadMiddlewares = $this->crawler->settings->getDefaultSettings()['DOWNLOAD_MIDDLEWARES'];
//        foreach($downloadMiddlewares as $middleware) {
//            $middlewareClass = new $middleware();
//            $middlewareName = str_replace('middleware', '', $middleware);
//            $this->pushMiddleware($middlewareClass, $middlewareName);
//        }
//    }

    /**
     * 添加中间件.
     *
     * @param object $middleware
     * @param string   $name
     *
     * @return $this
     */
//    public function pushMiddleware($middleware, string $name = null)
//    {
//        if (!is_null($name)) {
//            $this->middlewares[$name] = $middleware;
//        } else {
//            array_push($this->middlewares, $middleware);
//        }
//
//        return $this;
//    }

    /**
     * 创建一个handler stack. 用来装载中间件
     *
     * @return \GuzzleHttp\HandlerStack
     */
//    public function getHandlerStack(): HandlerStack
//    {
//        if ($this->handlerStack) {
//            return $this->handlerStack;
//        }
//
//        $this->handlerStack = HandlerStack::create(\GuzzleHttp\choose_handler());
//
//        $this->handlerStack->push(Middleware::retry(function($retries,
//                                                             $request,
//                                                             $response = null,
//                                                             $exception = null) {
//            return false;
//        }, function($numberOfRetries) { var_dump($numberOfRetries);return 1000;}), 'retry');
//
//        foreach ($this->middlewares as $name => $middleware) {
//            if(method_exists($middleware,'processRequest')){
//                $this->handlerStack->push(Middleware::mapRequest($middleware->processRequest()), $name);
//            }
//
//            if(method_exists($middleware,'processResponse')){
//                $this->handlerStack->push(Middleware::mapResponse($middleware->processResponse()), $name);
//            } else if(method_exists($middleware,'processResponseWithRequest')){
//                $this->handlerStack->push($middleware->processResponseWithRequest(), $name);
//            }
//        }
//
//
//        return $this->handlerStack;
//    }
//
//    protected function retryDelay()
//    {
//        return function ($numberOfRetries) {
//            return 1000 * $numberOfRetries;
//        };
//    }
//
//    public function retryDecider() {
//        return function (
//            $retries,
//            Request $request,
//            Response $response = null,
//            RequestException $exception = null
//        ) {
//            // 超过最大重试次数，不再重试
//            if ($retries >= 3) {
//                return false;
//            }
//
//            // 请求失败，继续重试
//            if ($exception instanceof ConnectException) {
//                return true;
//            }
//
//            if ($response) {
//                // 如果请求有响应，但是状态码大于等于500，继续重试(这里根据自己的业务而定)
//                if ($response->getStatusCode() >= 500) {
//                    return true;
//                }
//            }
//            var_dump(1234);
//
//            return false;
//        };
//    }

    public function downloaded(ResponseInterface $response, $index) {
        $response = Response::buildFromPsrResponse($response);
        if($response->getCallBack()) {
            call_user_func([$this->crawler->spider, $response->getCallBack()], $response);
        } else {
            $this->crawler->spider->parse($response, $index);
        }

    }

    public function downloadedFailed(RequestException $reason, $index) {
        var_dump($reason->getMessage());
    }

    public function __invoke($object, $index) {
        if($object instanceof ResponseInterface) {
            $this->downloaded($object, $index);
        } else if($object instanceof RequestException) {
            $this->downloadedFailed($object, $index);
        }
    }
}