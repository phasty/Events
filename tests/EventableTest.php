<?php
class EventableTest extends \PHPUnit_Framework_TestCase {
    public function testEventability() {
        $button = new Daem\Events\Eventable;
        $clickCount = 0;
        $button->on("click", function() use (&$clickCount) {
            $clickCount++;
        });
        $button->trigger("click");
        $this->assertEquals(1, $clickCount, "Обработчик события не сработал");

        $button1 = new Daem\Events\Eventable;
        $button1->on(null, $button);
        $button1->on("click", $button);
        $button1->trigger("click");

        $this->assertEquals(3, $clickCount, "Обработчик события не сработал");
    }
}
