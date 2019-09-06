<?php
/**
 * This file is part of the wengo/basesdk.
 *
 * (c) basesdk <398711943@qq.com>
 *
 */

namespace Haixun\Settings;


use Haixun\Core\Downloader;
use Haixun\Core\Scheduler;
use Haixun\DownloaderMiddlewares\UserAgentMiddleware;

class DefaultSettings
{
    public static $defaultSettings;

    public static function getDefaultSettings() {
        if(self::$defaultSettings) {
            return self::$defaultSettings;
        }

        self::$defaultSettings = [
            'DOWNLOAD_TIMEOUT' => '180',
            'DOWNLOADER' => Downloader::class,
            'DOWNLOAD_HANDLERS' => [UserAgentMiddleware::class],
            'SCHEDULER' => Scheduler::class,
        ];

        return self::$defaultSettings;
    }
}