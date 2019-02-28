<?php

namespace Tests\Util;

use EventEmitter\EventEmitter;

class ExtendedTask extends EventEmitter
{
    public function __construct()
    {
        parent::__construct();

        $this->emit('classConstructed');
    }
    /**
     * @return ExtendedTask
     */
    public function runTask(): ExtendedTask
    {
        $this->emit('preTaskRun');

        $this->emit('postTaskRun');

        $this->emit('taskFinished');

        return $this;
    }
}