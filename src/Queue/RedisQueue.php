<?php
/**
 * This file is part of the wengo/basesdk.
 *
 * (c) basesdk <398711943@qq.com>
 *
 */

namespace Haixun\Queue;
use \Predis\Client;

class RedisQueue extends Queue
{
    private $key = 'crawler:requests';

    public function setQueue() {
        $this->queue = new Client();
    }

    public function getTotal() {
        return $this->queue->llen($this->key);
    }

    public function push($request) {
        $this->queue->rpush($this->key, serialize($request));
    }

    public function pop() {
        return unserialize($this->queue->lpop($this->key));
    }

}