<?php
/**
 * This file is part of the wengo/basesdk.
 *
 * (c) basesdk <398711943@qq.com>
 *
 */

namespace Haixun\Queue;


class FifoQueue extends Queue
{
    public function push($request) {
        if(!in_array($request, $this->queue)) {
            array_push($this->queue, $request);
        }
    }

    public function pop() {
        return array_pop($this->queue);
    }
}