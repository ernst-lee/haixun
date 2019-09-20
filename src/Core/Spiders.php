<?php

namespace Haixun\Core;



use GuzzleHttp\Psr7\Request;


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
        echo $this->crawler->getState();
    }
}