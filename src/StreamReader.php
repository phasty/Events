<?php
namespace Daem\Events {
    use \Daem\Log\File as log;
    /*
     * Читает события из потока и воспроизводит их на слушателях
     */
    class StreamReader extends Eventable {

        /*
         * Список слушателей
         */
        protected $listeners = [];
        protected $stream = null;

        /*
         * Конструктор
         *
         * @param \Daem\Stream\Reader Обертка читающего потока
         */
        public function __construct(\Daem\Stream\Stream $stream) {
            $this->stream = $stream;
            $stream->on("data", [$this, "onData"]);
        }

        /*
         * Возвращает длину первого сообщения в буфере
         *
         * @return mixed Длина сообщения, либо false, если пришло менее 4 байт
         */
        protected function getMessageLength() {
            return strlen($this->messages) < 4 ?
                false
                    :
                unpack("N", $this->messages)[1];
        }

        /*
         * Пытается извлечь первое сообщение из буфера
         *
         * @return mixed Сообщение, либо false, если нет полных сообщений
         */
        protected function extractMessage() {
            if (empty($this->messages)) {
                return false;
            }
            if (false === $msgLen = $this->getMessageLength()) {
                return false;
            }

            if (strlen($this->messages) - 4 < $msgLen) {
                return false;
            }
            $this->messages = substr($this->messages, 4);
            $message = substr($this->messages, 0, $msgLen);
            $this->messages = substr($this->messages, $msgLen);

            return $message;
        }

        /*
         * Добавляет слушателя событий
         *
         * @param Eventable Слушатель
         */
        public function addListener(Eventable $listener) {
            $this->listeners []= $listener;
        }

        /*
         * Удаляет слушателя событий
         *
         * @param Eventable Слушатель
         */
        public function removeListener(Eventable $listener) {
            if (false === $index = array_search($listener, $this->listeners)) {
                return false;
            }
            unset($this->listeners[ $index ]);
            return true;
        }

        /*
         * Получает данные из потока и обрабатывает их
         *
         * Данные буферизуются. По мере поступления полных сообщений
         * они обрабатываются
         */
        public function onData(Event $event) {
            $this->messages .= $data = $event->getData();
            $i = 0;
            while (false !== $msg = $this->extractMessage()) {
                $i++;
                $eventObject = unserialize($msg);
                foreach ($this->listeners as $listener) {
                    $listener->trigger($eventObject);
                }
            }
            if ($this->messages && !$i) {
                $msg = $this->messages;
                if (strlen($msg) < 100) {
                    $msg = "0x" . bin2hex($msg);
                }
                log::error("Got broken message:\n$msg");
                log::error("last data: " . var_export($data, 1));
            }
        }
    }
}
