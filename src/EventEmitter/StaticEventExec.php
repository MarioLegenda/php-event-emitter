<?php

namespace EventEmitter;

class StaticEventExec
{
    /**
     * @param Event $event
     */
    public static function call(Event $event)
    {
        if ($event->getValue() instanceof \Throwable) {
            static::handleExceptionEvent($event);

            return;
        }

        static::handleCallableEvent($event);
    }
    /**
     * @param Event $event
     */
    private static function handleExceptionEvent(Event $event)
    {
        $values = $event->getValue();
        $callback = $event->getCallback();

        if ($callback instanceof CallbackInterface) {
            $callback->run([$values]);

            return;
        }

        $callback($values);

        return;
    }
    /**
     * @param Event $event
     */
    private static function handleCallableEvent(Event $event)
    {
        $values = $event->getValue();
        $callback = $event->getCallback();

        if ($callback instanceof CallbackInterface) {
            $callback->run(...$values);

            return;
        }

        $callback(...$values);
    }
}