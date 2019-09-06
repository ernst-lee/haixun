<?php
namespace Haixun\Core\Downloader;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Haixun\Http\Response;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
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

    public function __construct($crawler) {
        $this->crawler = $crawler;
        $this->downloaderMiddlewareManager = new DownloaderMiddlewareManager($this->crawler->settings->get('DOWNLOAD_MIDDLEWARES'));

//        if($this->crawler->settings->get('LOG')) {
//            $logger = new Logger('name');
//            $logger->pushHandler(new StreamHandler('your.log', $this->crawler->settings->get('LOG')));
//            $this->downloaderMiddlewareManager->pushMiddleware(new \Gmponos\GuzzleLogger\Middleware\LoggerMiddleware($logger, null, false, true), 'LoggerMiddleware');
//        }
    }
}