<?php
namespace Daem\Events {
    interface EventableInterface {
        /*
         * Добавить обработчик на событие
         *
         * @param $event        string  Наименование события
         * @param $callbacks    mixed   Обработчик или массив обработчиков события
         */
        public function on($event, $callbacks);

        /*
         * Удалить обработчик(и) с события
         *
         * @param $event string Наименование события
         * @param $callbacks    mixed   Обработчик или массив обработчиков события
         */
        public function off($event, $callbacks = null);

        /*
         * Вызвать обработчики события
         *
         * @param $event        string  Наименование события
         * @param $eventObject  mixed   Объект события
         */
        public function trigger($event, $eventObject = null);

        /*
         * Возвращает, установлен ли хотя бы один обработчик для события
         * в текущем процессе
         *
         * @param $event string Наименование события
         *
         * @return bool
         */
        public function hasEventHandler($event);

        /*
         * Возвращает список событий, на которые есть подписчики
         */
        public function getHandledEvents();
    }
}
