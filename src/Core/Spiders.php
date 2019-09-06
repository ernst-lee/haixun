<?php
/**
 * This file is part of the wengo/basesdk.
 *
 * (c) basesdk <398711943@qq.com>
 *
 */

namespace Haixun\Core;



use GuzzleHttp\Psr7\Request;
use Haixun\Core\Traits\Stats;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Spiders
{
    public $startUrls;
    /**
     * crawler
     * @var array
     */
    protected $crawler;

    public function setCrawler($crawler) {
        $this->crawler = $crawler;
    }

    public function startRequests() {
        if(!empty($this->startUrls)) {
            foreach($this->startUrls as $url) {
                yield new Request("GET", $url);
            }
        }
    }

    public function parse(\Haixun\Http\Response $response, $index) {

    }

    public function finish() {
        echo $this->crawler->stats;
    }
}