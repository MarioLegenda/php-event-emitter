<?php

namespace EventEmitter;

class EventEmitter {
    /**
     * @var EventCollection $listenerCollection
     */
    private $eventCollection;
    /**
     * @var bool $handleErrors
     */
    private $handleErrors;
    /**
     * @var \Throwable $internalError
     */
    private $internalError;
    /**
     * EventEmitter constructor.
     * @param bool $handleErrors
     */
    public function __construct(bool $handleErrors = false)
    {
        $this->eventCollection = new EventCollection();
        $this->handleErrors = $handleErrors;
    }
    /**
     * @param string $eventName
     * @param $callback
     * @return EventEmitter
     */
    public function on(string $eventName, $callback): EventEmitter
    {
        if ($this->internalError instanceof \Exception) {
            return $this;
        }

        if (!$this->eventCollection->has($eventName)) {
            $message = sprintf(
                'EventEmitter: No event registered with name \'%s\'',
                $eventName
            );

            throw new \RuntimeException($message);
        }

        /** @var Event $event */
        $event = $this->eventCollection->get($eventName);
        $event->setCallback($callback);

        if ($this->handleErrors) {
            try {
                StaticEventExec::call($event);
            } catch (\Throwable $exception) {
                $this->internalError = $exception;

                return $this;
            }
        }

        StaticEventExec::call($event);

        return $this;
    }
    /**
     * @param $callback
     */
    public function exception($callback)
    {
        $event = new Event('internalError', $this->internalError, $callback);

        StaticEventExec::call($event);

        $this->internalError = null;
    }
    /**
     * @param string $eventName
     * @param bool $strictCheck
     * @throws \InvalidArgumentException
     * @return bool
     *
     * Removes an event from the EventCollection. If the $strictCheck is set to true, it will throw
     * an exception if the event does not exists. Otherwise, it will return true if the event was removed or
     * false if there was no event to remove.
     */
    public function removeEvent(string $eventName, bool $strictCheck = false): bool
    {
        if ($strictCheck) {
            if (!$this->eventCollection->has($eventName)) {
                $message = sprintf(
                    'EventEmitter: Event \'%s\' does not exist and cannot be removed. If you want to suppress this exception, call EventEmitter::removeEvent() with the second argument set to false.',
                    $eventName
                );

                throw new \InvalidArgumentException($message);
            }
        }

        return $this->eventCollection->remove($eventName);
    }
    /**
     * @void
     *
     * Remove all events and reset the EventCollection
     */
    public function removeAllEvents()
    {
        $this->eventCollection->clear();
    }
    /**
     * @return array|null
     *
     * Returns all the event names as an associative string or null if there are not events
     */
    public function getEventNames(): ?array
    {
        return $this->eventCollection->getEventNames();
    }
    /**
     * @param string $eventName
     * @param $value
     *
     * Signals that the event is ready for execution. Has to be called before EventEmitter::on()
     */
    public function emit(string $eventName, ...$value)
    {
        $this->eventCollection->add(
            new Event($eventName, $value)
        );
    }
}