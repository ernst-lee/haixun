<?php
/**
 * This file is part of the wengo/basesdk.
 *
 * (c) basesdk <398711943@qq.com>
 *
 */

namespace Haixun\Core;
use Haixun\Http\Request;
use Haixun\Queue\FifoQueue;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 *  处理请求的入队和出队
 *  处理请求是否重复
 */
class Scheduler
{

    /**
     * @var array
     */
    protected $queue;

    /**
     * Scheduler constructor.
     */
    public function __construct(){
        $this->queue = new FifoQueue();
    }

    /**
     * 推送初始请求进队列
     */
    public function open($spiers) {
        foreach($spiers->startUrls as $url) {
            $this->enqueueRequest($url);
        }
    }

    /**
     * 检测队列是否还有请求
     * @return int
     */
    public function hasPendingRequest() {
        return sizeof($this->queue->queue);
    }

    /**
     * 入队列
     * @var RequestInterface
     * @return int
     */
    public function enqueueRequest(RequestInterface $request) {
        $this->queue->push($request);
    }

    /**
     * 出队列
     * @return \GuzzleHttp\Psr7\Request
     */
    public function nextRequest() {
        while($this->hasPendingRequest() > 0) {
            yield $this->queue->pop();
        }
    }
}