# haixun

# 安装


## 环境要求

> - PHP >= 7.0
> - [PHP cURL 扩展](http://php.net/manual/en/book.curl.php)
> - [PHP OpenSSL 扩展](http://php.net/manual/en/book.openssl.php)

## 安装

使用 [composer](http://getcomposer.org/):

```shell
$ composer require wengoooo/haixun
```

## 快速开始

> 建立一个爬虫
```php 
require_once "vendor/autoload.php";

use GuzzleHttp\Psr7\Request;
class TheBaseSpider extends \Haixun\Core\Spiders {
    public $maxPage = 1;
    public $currentPage = 1;
    public $userId;

//    public $startUrls = ['http://www.httpbin.org/get', 'http://www.httpbin.org/user-agent'];

    public function startRequests()
    {
        yield new Request("GET", "https://www.domain.com/categories/1735750");
    }

    public function parse(Haixun\Http\Response $response, $index)
    {
        if (sizeof($response->css("#max_page")) > 0) {
            $this->maxPage = (int)$response->css("#max_page")->text();
            $this->currentPage = 1;
            preg_match_all("%(user_[^']+)%", $response->getBodyContents(), $result, PREG_PATTERN_ORDER);
            $this->userId = $result[0][0];
        }

        $uri = new \GuzzleHttp\Psr7\Uri($response->getCurrentUrl());

        while ($this->currentPage++ <= $this->maxPage) {
            yield new Request("GET", sprintf("https://%s/load_items/categories/1735750/%s/%s/0", $uri->getHost(), $this->currentPage, $this->userId));
        }

        foreach ($response->css(".item a[href*=items]")->links() as $link) {
            yield new Request("GET", $link->getUri(), ['meta' => ['callback' => 'parseProduct']]);
        }

    }

    public function parseProduct(Haixun\Http\Response $response, $index) {
        var_dump($response->css("h2.itemTitle")->text());
    }

    public function finish() {}
}
```

> 启动爬虫
```php
$crawler = new \Haixun\Core\Crawler(new TheBaseSpider());
$crawler->crawl();
```