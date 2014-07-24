<?php
namespace Daem\Events {
    trait EventableTrait {
        private $events = [];
        private $any    = [];

        /*
         * Добавить обработчик на событие
         *
         * Прототип функции обратного вызова:
         * function (\Daem\Events\Event $event, \Daem\Events\EventableInterface $that)
         * $event - объект события, $that - объект, на котором произошло событие
         *
         * @param $event mixin Название события или null, если обработчики для всех событий объекта
         * @param $callbacks mixin Коллбэк или один коллбэк
         */
        public function on($event, $callback) {
            if ($event) {
                if (!isset($this->events[ $event ])) {
                    $this->events[ $event ] = [];
                }
                $setIn = &$this->events[ $event ];
            } else {
                $setIn = &$this->any;
            }
            if (is_string($callback)) {
                $callback = [ $this, $callback ];
            }
            if (array_search($callback, $setIn) === false) {
                $setIn []= $callback;
            }
            return $this;
        }

        private function offInArray(&$searchIn, $callback) {
            if (empty($searchIn)) {
                return;
            }
            if (is_null($callback)) {
                $searchIn = [];
                return;
            }
            $index = array_search($callback, $searchIn);
            if ($index === false) {
                return;
            }
            unset($searchIn[ $index ]);
        }

        /*
         * Удалить обработчик с события
         */
        public function off($events = null, $callback = null) {
            if (!$events) {
                $this->events = [];
            } else {
                foreach ((array)$events as $event) {
                    if (isset($this->events[ $event ])) {
                        $this->offInArray($this->events[ $event ], $callback);
                    }
                }
            }

            $this->offInArray($this->any, $callback);
            return $this;
        }

        /*
         * Воспроизвести событие на объекте
         *
         * @param $event        mixed Объект \Daem\Eventable\Event или имя события
         * @param $eventObject  mixed Данные события или предыдущее событие
         *
         * @return Возвращает результат последнего выполненного коллбэка
         */
        public function trigger($event, $eventObject = null) {
            if (!$this->hasEventHandler($event)) {
                return;
            }
            $searchArray = [ $this->any ];
            $eventName = $event instanceof Event ? $event->getName() : $event;
            if (!empty($this->events[ $eventName ])) {
                $searchArray []= $this->events[ $eventName ];
            }
            if ($event instanceof Event) {
                $event->previous = $eventObject;
            } else {
                $event = new Event($eventName, $eventObject);
            }

            $return = null;
            foreach ($searchArray as $searchIn) {
                if (!empty($searchIn)) {
                    foreach ($searchIn as $callback) {
                        if ($callback instanceof EventableInterface) {
                            $callback->trigger($event);
                        } else {
                            $return = call_user_func_array($callback, [$event, $this]);
                            if ($return === false) {
                                return $return;
                            }
                        }
                    }
                }

            }
            return $return;
        }

        public function hasEventHandler($event) {
            $eventName = $event instanceof Event ? $event->getName() : $event;
            return !empty($this->events[ $eventName ]) || !empty($this->any);
        }

        public function getHandledEvents() {
            return array_keys($this->events);
        }
        public function __serialize() {

        }
    }
}
