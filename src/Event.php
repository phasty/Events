<?php
namespace Daem\Events {
    class Event {
        protected $time = 0;
        protected $eventName = null;

        protected $data = null;

        public function __construct($eventName, $data = null) {
            $this->eventName = $eventName;
            $this->time = time();
            $this->data = $data;
        }

        public function getName() {
            return $this->eventName;
        }

        public function getTime() {
            return $this->time;
        }

        public function getData() {
            return $this->data;
        }
        public function __toString() {
            $data = serialize($this);
            return pack("N", strlen($data)) . $data;
        }
    }
}
