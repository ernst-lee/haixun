<?php
namespace Haixun\Queue;


abstract class Queue
{
    public $crawler;

    public $queue;
    /**
     * Queue constructor.
     */
    public function __construct($crawler) {
        $this->crawler = $crawler;
        $this->setQueue();
    }

    public function setQueue() {
        $this->queue = array();
    }

    public function getTotal() {
        return sizeof($this->queue);
    }

    abstract public function push($request);
    abstract public function pop();


}