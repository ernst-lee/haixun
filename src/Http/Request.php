<?php
/**
 * This file is part of the wengo/basesdk.
 *
 * (c) basesdk <398711943@qq.com>
 *
 */

namespace Haixun\Http;


class Request extends \GuzzleHttp\Psr7\Request {
    public $meta = [];
    public function __construct(
        $method,
        $uri,
        array $options = [],
        $version = '1.1'
    ) {
        if(isset($options['headers'])) {
            $headers = $options['headers'];
        } else {
            $headers = [];
        }

        if(isset($options['body'])) {
            $body = $options['body'];
        } else {
            $body = null;
        }

        if(isset($options['meta'])) {
            $this->meta = $options['meta'];
        }

        parent::__construct($method, $uri, $headers, $body, $version);
    }

    public function getMeta() {
        return $this->meta;
    }
}