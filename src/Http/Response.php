<?php
namespace Haixun\Http;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Response extends GuzzleResponse {

    /**
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    public $dom;

    /**
     * 获取内容
     * @return string
     */
    public function getBodyContents() {
        $this->getBody()->rewind();
        $contents = $this->getBody()->getContents();
        $this->getBody()->rewind();

        return $contents;
    }

    /**
     * 获取当前链接
     * @return string
     */
    public function getCurrentUrl() {
        return current($this->getHeader('x-guzzle-effective-url'));
    }

    /**
     * 获取META
     * @return string
     */
    public function getMeta() {
        return $this->getHeader('X-GUZZLE-META');
    }

    /**
     * 获取回调函数
     * @return callback
     */
    public function getCallBack() {
        if($this->getMeta() && isset($this->getMeta()['callback'])) {
            return $this->getMeta()['callback'];
        }

        return null;
    }

    /**
     * 获取解析器
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function getDom() {
        if(!$this->dom) {
            $this->dom = new DomCrawler($this->getBodyContents(), $this->getCurrentUrl());
        }

        return $this->dom;
    }

    /**
     * css方式解析
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function css($query) : \Symfony\Component\DomCrawler\Crawler{
        return $this->getDom()->filter($query);
    }

    /**
     * xpath方式解析
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function xpath($query) {
        return $this->getDom()->filterXPath($query);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Haixun\Http\Response
     */
    public static function buildFromPsrResponse(ResponseInterface $response) {
        return new static(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    /**
     * @return bool|string
     */
    public function __toString() {
        return $this->getBodyContents();
    }
}