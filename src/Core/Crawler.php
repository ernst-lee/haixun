<?php
namespace Haixun\Core;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Haixun\Settings;

class Crawler {

    /**
     * @var \Haixun\Core\Spiders
     */
    public $spider;

    /**
     * @var \Haixun\Core\Engine
     */
    public $engine;

    /**
     * @var \Haixun\Settings
     */
    public $settings;

    public $stats;

    /**
     * 缺省设置
     * @var array
     */
    protected static $defaults = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ],
    ];

    /**
     * Crawlers constructor.
     */
    public function __construct($spidercls, $options = []) {
        $this->settings = new Settings($options);
        $this->engine = new Engine($this);
        $this->spider = $spidercls;
        $this->spider->setCrawler($this);
    }

    public function getState() {
        if(!$this->stats) {
            $this->stats = new Stats();
        }

        return $this->stats;
    }

    public function getConfig() {
        return $this->settings->getConfig();
    }

    /**
     * 开始爬取
     */
    public function crawl(...$args) {
        $this->engine->openSpider($this->spider); # 开始获取初始的请求
    }
}