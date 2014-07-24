Events
======

Package supplies support of event handling on different kind of objects. To you this you just inherite you class
from Phasty\Events\Eventable or use Phasty\Events\EventableTrait trait within you class:

    class SomeCoolClass extends Phasty\Events\Eventable {
    } 

    $obj = new SomeCoolClass;
    $obj->on("hello-event", function($event) {
        echo $event->getData();
    });
    $obj->trigger("hello-event", "Hello world");
  
And more:

    class Button extends Phasty\Events\Eventable {
        public function __construct() {
            $this->on("click", "sayHello");
            $this->on("click", "sayGoodbye");
        }
        protected function sayHello() {
            echo "Hello!\n";
        }
        protected function sayGoodbye() {
            echo "Bye!\n";
        }
        public function click() {
            $this->trigger("before-click");
            $this->trigger("click");
        }
    }

    $btn = new Button;
    $btn->on("before-click", function () {
        echo "Before click handler\n";
    });
    $btn->click();
    $btn->off("click", "sayHello");
    $btn->click();

USAGE
-----
    // Listen to some-event
    $obj->on("some-event", /* Any PHP callback or method name of $obj class */);
    // Listen to any event
    $obj->on(null, /* Any PHP callback or method name of $obj class */);
    // Forget all events callbacks
    $obj->off();
    // Forget all callbacks of "some-event" event
    $obj->off("some-event");
    // Forget exact callback for "some-event"
    $obj->off("some-event", /* Any PHP callback or method name of $obj class */);
