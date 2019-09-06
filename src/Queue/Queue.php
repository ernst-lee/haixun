<?php
/**
 * This file is part of the wengo/basesdk.
 *
 * (c) basesdk <398711943@qq.com>
 *
 */

namespace Haixun\Queue;


abstract class Queue
{
    public $queue;
    /**
     * Queue constructor.
     */
    public function __construct() {
        $this->queue = array();
    }

    abstract public function push($request);
    abstract public function pop();


}