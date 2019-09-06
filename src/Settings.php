<?php
/**
 * This file is part of the wengo/basesdk.
 *
 * (c) basesdk <398711943@qq.com>
 *
 */

namespace Haixun;

use GuzzleHttp\Middleware;

use Haixun\Core\Downloader\Downloader;
use Haixun\Core\Scheduler;
use Haixun\Core\Spiders;
use Haixun\DownloaderMiddlewares\EffectiveUrlMiddleware;
use Haixun\DownloaderMiddlewares\MetaMiddleware;
use Haixun\DownloaderMiddlewares\RetryAgentMiddleware;
use Haixun\DownloaderMiddlewares\UserAgentMiddleware;
use Haixun\Core\Support\Collection;
use Haixun\DownloaderMiddlewares\LoggerMiddleware;
use Monolog\Logger;

class Settings {
    public $defaultConfig;

    /**
     * @var array
     */
    protected $userConfig = [];

    public function __construct($userConfig) {
        $this->userConfig = $userConfig;
    }

    public function getConfig() {
        $this->defaultConfig = [
            'DOWNLOAD_TIMEOUT'          => 180,
            'CONCURRENT_REQUESTS'       => 16,
            'LOG'                       => Logger::DEBUG,
            'DEBUG'                     => false,
            'CURL' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
            'DOWNLOADER'                => Downloader::class,
            'DOWNLOAD_MIDDLEWARES'      => [
                ['class' => UserAgentMiddleware::class],
                ['class' => EffectiveUrlMiddleware::class],
                ['class' => MetaMiddleware::class],
                ['class' => RetryAgentMiddleware::class],
//                ['class' => LoggerMiddleware::class],
            ],
            'SCHEDULER'                 => Scheduler::class,
        ];

        return new Collection(array_replace_recursive($this->defaultConfig, $this->userConfig));
    }

    public function __call($name, $arguments) {
        return $this->getConfig()->get(current($arguments));
    }
}