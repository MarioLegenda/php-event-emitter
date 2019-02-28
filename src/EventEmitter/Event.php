<?php

namespace EventEmitter;

class Event
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var object|\Closure
     */
    private $callback;
    /**
     * @var mixed
     */
    private $value;
    /**
     * Event constructor.
     * @param string $name
     * @param $callback
     * @param $value
     */
    public function __construct(
        string $name,
        $value,
        $callback = null
    ) {
        if (!is_null($callback)) {
            $this->validateCallback($callback);
        }

        $this->name = $name;
        $this->callback = $callback;
        $this->value = $value;
    }
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * @return \Closure|object
     */
    public function getCallback()
    {
        return $this->callback;
    }
    /**
     * @param \Closure|CallbackInterface $callback
     */
    public function setCallback($callback)
    {
        $this->validateCallback($callback);

        $this->callback = $callback;
    }
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param $value
     *
     */
    public function setValue(array $value = null)
    {
        $this->value = $value;
    }
    /**
     * @param \Closure|CallbackInterface $callback
     */
    private function validateCallback($callback)
    {
        if (!$callback instanceof CallbackInterface and !$callback instanceof \Closure) {
            $message = sprintf(
                'EventEmitter: Invalid event callback. Callback has to be either an implementation of %s or a %s (anonymous function)',
                CallbackInterface::class,
                \Closure::class
            );

            throw new \InvalidArgumentException($message);
        }
    }
}