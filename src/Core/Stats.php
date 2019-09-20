<?php

namespace Haixun\Core;


class Stats {
    public $stats;

    public $startTime;
    public $endTime;

    public function setStartTime() {
        $this->startTime = date("Y-m-d H:i:s");
    }

    public function setEndTime() {
        $this->endTime = date("Y-m-d H:i:s");
    }

    public function addState($value) {
        $this->stats[] = $value;
    }

    public function getState($key) {
        return $this->stats[$key];
    }

    public function getDateDiff($start_time, $end_time='') {
        $end_time = ($end_time=='')?date("Y-m-d H:i:s"):$end_time;
        $datetime1 = new \DateTime($start_time);
        $datetime2 = new \DateTime($end_time);
        $interval = $datetime1->diff($datetime2);

//        $time['a'] = $interval->format('%a');    // 两个时间相差总天数
        return $interval->format("%Y-%m-%d %H:%i:%s");
    }

    public function __toString()
    {
        $output = "Dumping stats:\r\n";
        $output .= "{\r\n";
        $output .= sprintf("'%s': %s,\r\n", 'start_time', $this->startTime);
        $output .= sprintf("'%s': %s,\r\n", 'end_time', $this->endTime);
        $output .= sprintf("'%s': %s,\r\n", 'date_diff', $this->getDateDiff($this->startTime, $this->endTime));
        foreach(array_count_values($this->stats) as $key => $value) {
            $output .= sprintf("'%s': %s,\r\n", $key, $value);
        }

        $output .= "}";

        return $output;
    }
}