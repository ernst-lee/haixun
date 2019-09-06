<?php
/**
 * This file is part of the wengo/basesdk.
 *
 * (c) basesdk <398711943@qq.com>
 *
 */

namespace Haixun\Core;


class Stats {
    public $stats;

    public function set($value) {
        $this->stats[] = $value;
    }

    public function get($key) {
        return $this->stats[$key];
    }

    public function __toString()
    {
        $output = "Dumping stats:\r\n";
        $output .= "{\r\n";
        foreach(array_count_values($this->stats) as $key => $value) {
            $output .= sprintf("'%s': %s,\r\n", $key, $value);
        }

        $output .= "}";

        return $output;
    }
}