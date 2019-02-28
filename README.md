## Introduction

This project is a light port of Nodes [EventEmitter class](https://nodejs.org/api/events.html).
Although, it does not have all the features that Node EventEmitter has, it has all
that you need to implement the Observer pattern right away in your classes with minimal
code refactoring.

## Installation

Supports PHP >7.0

`composer require mario-legenda/php-event-emitter`

#### Usage by extending the EventEmitter class

```
class Task extends EventEmitter
{
    public function __construct()
    {
        parent::__construct(); // don't forget to call the parent Event Emitter constructor

        $this->emit('classConstructed');
    }

    public function runTask(): ExtendedTask
    {
        $this->emit('preRunEvent');
        
        // some task specific code

        $this->emit('postRunEvent');

        $this->emit('taskFinished');

        return $this;
    }
}

$task = new Task();

$task
    ->runTask()
    ->on('classConstructed', function($val) {
        // called when the classConstructed event is emitted
    })
    ->on('preRunEvent', function($val) {
        // called when the preRunEvent event is emitted
    })
    ->on('postRunEvent', function($val) {
        // called when the postRunEvent event is emitted
    })
    ->on('taskFinished', function($val) {
        // called when the taskFinished event is emitted
    })
```

An alternative to calling EventEmitter::on() with a closure would be to
create a class that implements the EventEmitter\CallableInterface that exposes
a single method `run()`. The drawback is that in PHP, we cannot set a variable
number of parameters for the `run()` method. Therefor, if you want to use this interface,
you would have to nullify every one of the arguments that you expect to receive.
For example...

```

use EventEmitter\CallableInterface;

class Callable implements CallableInterface 
{
    public function run($argumentOne = null, $argumentTwo = null) 
    {
    }
}
```

#### Multiple event handlers

It is possible to chain multiple identical events

```
$eventEmitter = new EventEmitter();

$eventEmitter->emit('event', 'some value');

$eventEmitter
    ->on('event', function($val) {
        // called once
    })
    ->on('event', function($val) {
       // called twice
    })
    ->on('event', function($val) {
       // called three times
    })
    
```

#### Usage within an object

```
class Task
{
    private $eventEmitter;
    
    public function __construct()
    {
        $this->eventEmitter = new EventEmitter();
    }

    public function runTask(): ExtendedTask
    {
        $this->eventEmitter->emit('preRunEvent');
        
        // some task specific code

        $this->eventEmitter->emit('postRunEvent');

        $this->eventEmitter->emit('taskFinished');

        return $this;
    }
    
    public function getEventEmitter(): EventEmitter 
    {
        return $this->eventEmitter;
    }
}

$task
    ->runTask()
    ->getEventEmitter()
    ->on('classConstructed', function($val) {
        // called when the classConstructed event is emitted
    })
    ->on('preRunEvent', function($val) {
        // called when the preRunEvent event is emitted
    })
    ->on('postRunEvent', function($val) {
        // called when the postRunEvent event is emitted
    })
    ->on('taskFinished', function($val) {
        // called when the taskFinished event is emitted
    })
```

As you can see, the two examples are almost identical. Creating the EventEmitter
instance inside the Task class is not proper dependency injection but it enables
us to use the EventEmitter seamlessly. The client code does not now that Task is using
(or is) an EventEmitter.

#### Exception handling

EventEmitter can handle internal exceptions thrown by your code.

```
$eventEmitter = new EventEmitter(true); // notice the argument. That means that exceptions will be handled by EventEmitter

$eventEmitter->emit('event', 'some value');

$eventEmitter
    ->on('event', function($val) {
        throw new \RuntimeException();
    })
    ->exception(function(\Throwable $e) {
        // $e instanceof RuntimeException
    })
```

If you chain multiple events, every event after the one where an exception occurred
will not be called.

```
$eventEmitter
    ->on('event', function($val) {
        throw new \RuntimeException();
    })
    ->on('otherEvent', function($val) {
        // this event is never called. Execution passes to the exception()
    })
    ->exception(function(\Throwable $e) {
        // $e instanceof RuntimeException
    })
```

## API

`EventEmitter::__construct($handleErrors = false)`

EventEmitter constructor. If provided `true` as the first argument, exceptions will
be handled by the EventEmitter. Else, exceptions will be propagated to client code

`EventEmitter::emit(string $eventName, ...args): void`

Registers an event to be called with `EventEmitter::on()`. Must be called before `EventEmitter::on()`.

`EventEmitter::on(string $eventName, $callback): EventEmitter`

Executes an event. `$callback` has to be an instance of `\Closure` or a instance of `EventEmitter\CallableInterface`

`EventEmitter::exception($callback)`

Called only when `$handleError` argument is set to `true` in the constructor and an exception occurred in 
executing one of the events.

`EventEmitter::removeEvent(string $eventName, bool $strictCheck = false): bool`

Removes an event and returns `true` if successful or `false` on failure. If `$strictCheck` is true,
an exception is throw if an event cannot be removed (if it doesn't exist for example).

`EventEmitter::removeAllEvents()`

Removes all events.

`EventEmitter::getEventNames(): ?array`

Returns an array of currently registered event names or `null` if there are none