<?php

namespace Tests;

use EventEmitter\CallbackInterface;
use PHPUnit\Framework\TestCase;
use Tests\Util\ExtendedTask;

class TaskTest extends TestCase
{
    public function testTaskWithClosure()
    {
        $extendedTask = new ExtendedTask();
        $classConstructed = false;
        $preTaskRun = false;
        $postTaskRun = false;
        $taskFinished = false;

        $extendedTask
            ->runTask()
            ->on('classConstructed', function() use (&$classConstructed) {
                $classConstructed = true;
            })
            ->on('preTaskRun', function() use (&$preTaskRun) {
                $preTaskRun = true;
            })
            ->on('postTaskRun', function() use (&$postTaskRun) {
                $postTaskRun = true;
            })
            ->on('taskFinished', function() use (&$taskFinished) {
                $taskFinished = true;
            });

        static::assertTrue($classConstructed);
        static::assertTrue($preTaskRun);
        static::assertTrue($postTaskRun);
        static::assertTrue($taskFinished);
    }

    public function testTaskWithCallbackObject()
    {
        $extendedTask = new ExtendedTask();

        $classConstructedCallback = new class() implements CallbackInterface
        {
            private $eventCalled = false;

            public function run($first = null)
            {
                $this->eventCalled = true;
            }

            public function getEventCalled(): bool
            {
                return $this->eventCalled;
            }
        };

        $preTaskRun = new class() implements CallbackInterface
        {
            private $eventCalled = false;

            public function run($first = null)
            {
                $this->eventCalled = true;
            }

            public function getEventCalled(): bool
            {
                return $this->eventCalled;
            }
        };

        $postTaskRun = new class() implements CallbackInterface
        {
            private $eventCalled = false;

            public function run($first = null)
            {
                $this->eventCalled = true;
            }

            public function getEventCalled(): bool
            {
                return $this->eventCalled;
            }
        };

        $taskFinished = new class() implements CallbackInterface
        {
            private $eventCalled = false;

            public function run($first = null)
            {
                $this->eventCalled = true;
            }

            public function getEventCalled(): bool
            {
                return $this->eventCalled;
            }
        };

        $extendedTask
            ->runTask()
            ->on('classConstructed', $classConstructedCallback)
            ->on('preTaskRun', $preTaskRun)
            ->on('postTaskRun', $postTaskRun)
            ->on('taskFinished', $taskFinished);

        static::assertTrue($classConstructedCallback->getEventCalled());
        static::assertTrue($preTaskRun->getEventCalled());
        static::assertTrue($postTaskRun->getEventCalled());
        static::assertTrue($taskFinished->getEventCalled());
    }
}