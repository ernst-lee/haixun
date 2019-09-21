<?php
namespace Haixun\Core\Downloader;



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
        $this->downloaderMiddlewareManager = new DownloaderMiddlewareManager($this->crawler->getConfig()->get('DOWNLOAD_MIDDLEWARES'));
    }
}