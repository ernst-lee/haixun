<?php
/**
 * This file is part of the wengo/basesdk.
 *
 * (c) basesdk <398711943@qq.com>
 *
 */

namespace Haixun\Core\Downloader;


use GuzzleHttp\HandlerStack;


class DownloaderMiddlewareManager
{
    /**
     * 处理者堆栈
     * @var \GuzzleHttp\HandlerStack
     */
    protected $handlerStack;

    /**
     * 中间件
     * @var array
     */
    protected $middlewares = [];

    /**
     * 下载中间件
     * @var array
     */
    protected $downloadMiddlewares = [];

    public function __construct($downloadMiddlewares) {
        $this->downloadMiddlewares = $downloadMiddlewares;
        $this->addBaseMiddleware();
    }

    /**
     * 添加基础中间件
     */
    public function addBaseMiddleware() {
        foreach($this->downloadMiddlewares as $middleware) {
            if(isset($middleware['args']) && !empty($middleware['args'])) {
                $middlewareClass = new $middleware['class']($middleware['args']);
            } else {
                $middlewareClass = new $middleware['class']();
            }
            $middlewareName = str_replace('middleware', '', get_class($middlewareClass));
            $this->pushMiddleware($middlewareClass, $middlewareName);
        }
    }

    /**
     * 添加中间件.
     *
     * @param object $middleware
     * @param string   $name
     *
     * @return $this
     */
    public function pushMiddleware($middleware, string $name = null) {
        if (!is_null($name)) {
            $this->middlewares[$name] = $middleware;
        } else {
            array_push($this->middlewares, $middleware);
        }

        return $this;
    }

    /**
     * 添加中间件到堆
     */
    public function pushMiddlewareToStack() {
        foreach ($this->middlewares as $name => $middleware) {
            if(method_exists($middleware,'processRequest')) {
                $this->handlerStack->push($middleware->processRequest(), $name);
            }

            if(method_exists($middleware,'processResponse')) {
                $this->handlerStack->push($middleware->processResponse(), $name);
            }

            if(!method_exists($middleware,'processRequest') && !method_exists($middleware,'processResponse')) {
                $this->handlerStack->push($middleware, $name);
            }
        }
    }

    public function getHandlerStack(): HandlerStack
    {
        if ($this->handlerStack) {
            return $this->handlerStack;
        }
        $this->handlerStack = HandlerStack::create(\GuzzleHttp\choose_handler());
        $this->pushMiddlewareToStack();
        return $this->handlerStack;
    }

}