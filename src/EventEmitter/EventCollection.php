<?php

namespace EventEmitter;

class EventCollection implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $listeners = [];
    /**
     * @param Event $event
     */
    public function add(Event $event)
    {
        $this->listeners[$event->getName()] = $event;
    }
    /**
     * @param string $eventName
     * @return Event|null
     */
    public function get(string $eventName): ?Event
    {
        if (!$this->has($eventName)) {
            return null;
        }

        return $this->listeners[$eventName];
    }
    /**
     * @param string $eventName
     * @return bool
     */
    public function has(string $eventName): bool
    {
        return isset($this->listeners[$eventName]);
    }
    /**
     * @param string $eventName
     * @return bool
     */
    public function remove(string $eventName): bool
    {
        if (!$this->has($eventName)) {
            return false;
        }

        unset($this->listeners[$eventName]);

        return true;
    }
    /**
     * @void
     *
     * Clears a specific event and its event listeners
     */
    public function clear()
    {
        $this->listeners = [];
    }
    /**
     * @return array|null
     */
    public function getEventNames(): ?array
    {
        if (empty($this->listeners)) {
            return null;
        }

        return array_keys($this->listeners);
    }
    /**
     * @return array|\Traversable
     */
    public function getIterator()
    {
        return $this->listeners;
    }
}