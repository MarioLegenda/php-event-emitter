<?php

namespace Tests;

use EventEmitter\CallbackInterface;
use EventEmitter\EventEmitter;
use PHPUnit\Framework\TestCase;

class EventEmitterTest extends TestCase
{
    public function testBasicEmittingWithClosure()
    {
        $eventEmitter = new EventEmitter();
        $eventCalled = false;
        $calledSecondTime = false;

        $eventEmitter->emit('event', 10);

        $eventEmitter
            ->on('event', function($val) use (&$eventCalled) {
                static::assertEquals(10, $val);
                $eventCalled = true;
            })
            ->on('event', function($val) use (&$calledSecondTime) {
                static::assertEquals(10, $val);
                $calledSecondTime = true;
            });

        static::assertTrue($eventCalled);
        static::assertTrue($calledSecondTime);
    }

    public function testBasicEventEmitterWithMultipleArguments()
    {
        $eventEmitter = new EventEmitter();
        $eventCalled = false;
        $calledSecondTime = false;

        $eventEmitter->emit('event', 10, 20, 30);

        $eventEmitter
            ->on('event', function($val1, $val2, $val3) use (&$eventCalled) {
                static::assertEquals(10, $val1);
                static::assertEquals(20, $val2);
                static::assertEquals(30, $val3);
                $eventCalled = true;
            })
            ->on('event', function($val1, $val2, $val3) use (&$calledSecondTime) {
                static::assertEquals(10, $val1);
                static::assertEquals(20, $val2);
                static::assertEquals(30, $val3);
                $calledSecondTime = true;
            });

        static::assertTrue($eventCalled);
        static::assertTrue($calledSecondTime);
    }

    public function testBasicEmittingWithCallbackObject()
    {
        $eventEmitter = new EventEmitter();

        $callback1 = new class() implements CallbackInterface
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

        $callback2 = new class() implements CallbackInterface
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

        $eventEmitter->emit('event', 10);

        $eventEmitter
            ->on('event', $callback1)
            ->on('event', $callback2);

        static::assertTrue($callback1->getEventCalled());
        static::assertTrue($callback2->getEventCalled());
    }

    public function testHelperFunctions()
    {
        $eventEmitter = new EventEmitter();
        $eventEmitter->emit('event1', 10);
        $eventEmitter->emit('event2', 20);

        $eventNames = $eventEmitter->getEventNames();

        static::assertNotEmpty($eventNames);
        static::assertEquals(2, count($eventNames));
        static::assertEquals('event1', $eventNames[0]);
        static::assertEquals('event2', $eventNames[1]);

        $removed = $eventEmitter->removeEvent('event1');

        static::assertTrue($removed);

        $eventNames = $eventEmitter->getEventNames();

        static::assertNotEmpty($eventNames);
        static::assertEquals(1, count($eventNames));
        static::assertEquals('event2', $eventNames[0]);

        $eventEmitter->removeAllEvents();

        $eventNames = $eventEmitter->getEventNames();

        static::assertEmpty($eventNames);
        static::assertNull($eventNames);

        $eventEmitter->emit('event1', 10);
        $eventEmitter->emit('event2', 20);

        $invalidArgumentExceptionCalled = false;
        try {
            $eventEmitter->removeEvent('non existent', true);
        } catch (\InvalidArgumentException $e) {
            $invalidArgumentExceptionCalled = true;
        }

        static::assertTrue($invalidArgumentExceptionCalled);
    }

    public function testEventEmitterExceptionHandling()
    {
        $eventEmitter = new EventEmitter(true);
        $eventCalled = false;
        $calledSecondTime = false;
        $exceptionCaught = false;

        $eventEmitter->emit('event', 10);
        $eventEmitter->emit('event2', 20);

        $eventEmitter
            ->on('event', function($val) use (&$eventCalled) {
                $eventCalled = true;

                throw new \RuntimeException('noop');
            })
            ->on('event2', function($val) use (&$calledSecondTime) {
                static::assertEquals(10, $val);
                $calledSecondTime = true;
            })
            ->exception(function(\Throwable $e) use (&$exceptionCaught) {
                $exceptionCaught = true;
            });

        static::assertTrue($eventCalled);
        static::assertFalse($calledSecondTime);
        static::assertTrue($exceptionCaught);
    }
}