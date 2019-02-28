<?php

namespace Tests;

use EventEmitter\EventEmitter;

class Task
{
    /**
     * @var EventEmitter $eventEmitter
     */
    private $eventEmitter;

    public function __construct()
    {
        $this->eventEmitter = new EventEmitter();
    }

    public function run()
    {

    }
}