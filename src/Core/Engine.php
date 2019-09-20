<?php
namespace Haixun\Core;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;


use Haixun\Http\Response;
use Psr\Http\Message\RequestInterface;


class Engine {
    /**
     * @var \Haixun\Core\Crawler
     */
    public $crawler;

    /**
     * @var \Haixun\Core\Downloader\Downloader
     */
    public $downloader;

    /**
     * @var \Haixun\Core\Scheduler
     */
    public $scheduler;

    /**
     * Crawlers constructor.
     */
    public function __construct(Crawler $crawler) {
        $this->crawler = $crawler;
        $this->downloader = $this->createDownloader();
        $this->scheduler = $this->createScheduler();

        $this->httpClient = new Client([
            'handler' => $this->downloader->downloaderMiddlewareManager->getHandlerStack(),
            'debug' => $this->crawler->getConfig()->get('DEBUG'),
            'timeout' => $this->crawler->getConfig()->get('DOWNLOAD_TIMEOUT'),
            'cookies' => $this->crawler->getConfig()->get('COOKIES_ENABLED')]
        );
    }

    /**
     * 实例化Downloader
     * @return \Haixun\Core\Downloader\Downloader
     */
    public function createDownloader() {
        $downloaderClass = $this->crawler->getConfig()->get('DOWNLOADER');
        return new $downloaderClass($this->crawler);
    }

    /**
     * 实例化Scheduler
     * @return \Haixun\Core\Scheduler
     */
    public function createScheduler() {
        $schedulerClass = $this->crawler->getConfig()->get('SCHEDULER');
        return new $schedulerClass($this->crawler);
    }

    /**
     * 启动爬虫
     */
    public function openSpider(Spiders $spiers) {
        foreach($spiers->startRequests() as $request) {
            $this->schedule($request);
            $this->crawler->getState()->setStartTime();
        }

        while ($this->scheduler->hasPendingRequest()) {
            $this->next_request();
        }

        $this->crawler->getState()->setEndTime();
        $this->crawler->spider->finish();

    }

    public function next_request() {
        $pool = new Pool($this->httpClient, $this->scheduler->nextRequest(), [
            'concurrency' => (int)$this->crawler->getConfig()->get("CONCURRENT_REQUESTS"),
//            'options' => $this->httpClient->getConfig(),
            'fulfilled' => $this->handleDownloadSuccess(),
            'rejected'  => $this->handleDownloaderFailed(),
        ]);

        $promise = $pool->promise();

        $promise->wait();
    }

    /**
     * 处理下载成功
     */
    public function handleDownloadSuccess() {
        return function($response, $index) {
//            $this->crawler->stats->set(sprintf('downloader/response_status_count/%s', $response->getStatusCode()));
            $this->crawler->getState()->addState(sprintf('downloader/response_status_count/%s', $response->getStatusCode()));
            $this->crawler->getState()->addState('downloader/response_count');
            $response = Response::buildFromPsrResponse($response);
            if($response->getCallBack()) {
                $next = call_user_func([$this->crawler->spider, $response->getCallBack()], $response, $index);
            } else {
                $next = $this->crawler->spider->parse($response, $index);
            }

            if($next instanceof Generator) {
                foreach($next as $request) {
                    $this->schedule($request);
                }
            }
        };
    }

    /**
     * 处理下载失败
     */
    public function handleDownloaderFailed() {
        return function($reason, $index) {
            var_dump($reason->getMessage());
            $this->crawler->getState()->addState(sprintf('downloader/response_status_count/%s', $reason->getCode()));
            $this->crawler->stats->set(sprintf('downloader/response_status_count/%s', $reason->getCode()));
        };
    }

    /**
     * 推送请求进队列
     */
    public function schedule(RequestInterface $request) {
        $this->scheduler->enqueueRequest($request);
    }
}