<?php
namespace Phasty\Events {
    class Eventable implements EventableInterface, \Serializable {
        use EventableTrait;

        public function serialize() {
            $data = get_object_vars($this);
            unset($data["events"]);
            unset($data["any"]);
            return serialize($data);
        }
        public function unserialize($serialized) {
            $data = unserialize($serialized);
            foreach ($data as $k => $v) {
                $this->$k = $v;
            }
        }
    }
}
