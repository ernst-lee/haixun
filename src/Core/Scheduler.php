<?php


namespace Haixun\Core;

use Haixun\Queue\FifoQueue;
use Haixun\Queue\RedisQueue;
use Psr\Http\Message\RequestInterface;
use Pleo\BloomFilter\BloomFilter;
/**
 *  处理请求的入队和出队
 *  处理请求是否重复
 */
class Scheduler
{
    /**
     * @var
     */
    public $crawler;

    /**
     * @var BloomFilter
     *
     */
    private $bloomFilter;

    private $approximateItemCount = 500000;

    private $falsePositiveProbability = 0.001;

    protected $queue;


    /**
     * Scheduler constructor.
     */
    public function __construct($crawler){
        $this->crawler = $crawler;

//        $this->queue = new RedisQueue($crawler);
        $this->queue = new FifoQueue($crawler);
        $this->bloomFilter = BloomFilter::init($this->getApproximateItemCount(), $this->getFalsePositiveProbability());
    }


    public function getApproximateItemCount() {
        return $this->approximateItemCount;
    }

    public function getFalsePositiveProbability() {
        return $this->falsePositiveProbability;
    }

    public function setApproximateItemCount($approximateItemCount) {
        return $this->approximateItemCount = $approximateItemCount;
    }

    public function setFalsePositiveProbability($falsePositiveProbability) {
        return $this->falsePositiveProbability = $falsePositiveProbability;
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
        return $this->queue->getTotal();
    }

    /**
     * 入队列
     * @var RequestInterface
     * @return int
     */
    public function enqueueRequest(RequestInterface $request) {
        if(!$this->bloomFilter->exists($request->getUri())) {
            $this->queue->push($request);
            $this->crawler->getState()->addState('scheduler/enqueued');
        } else {
            $this->crawler->getState()->addState('scheduler/duplicate');
        }
    }

    /**
     * 出队列
     * @return \GuzzleHttp\Psr7\Request
     */
    public function nextRequest() {
        while($this->hasPendingRequest() > 0) {
            $this->crawler->getState()->addState('scheduler/dequeued');
            yield $this->queue->pop();
        }
    }
}