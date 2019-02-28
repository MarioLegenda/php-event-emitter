<?php

namespace EventEmitter;

class StaticEventExec
{
    /**
     * @param Event $event
     */
    public static function call(Event $event)
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