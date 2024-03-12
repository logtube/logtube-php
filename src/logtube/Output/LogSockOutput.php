<?php

namespace Logtube\Output;

use DateTime;
use Logtube\IOutput;

class LogSockOutput implements IOutput
{

    private $_sock;

    private $_fd = false;

    /**
     * @param $_sock string, path to unix socket file
     */
    public function __construct($_sock)
    {
        $this->_sock = $_sock;
    }

    public function append($event)
    {
        if (!$this->_fd) {

            $this->_fd = stream_socket_client("unix://" . $this->_sock);

            if (!$this->_fd) {
                return;
            }
        }

        $j = [
            "timestamp" => $event->_timestamp->format(DateTime::ISO8601),
            "topic" => $event->_topic,
            "crid" => $event->_crid,
            "crsrc" => $event->_crsrc,
        ];
        if ($event->_keyword != null) {
            $j["keyword"] = $event->_keyword;
        }
        if ($event->_extra != null) {
            $j["extra"] = $event->_extra;
        }
        if ($event->_message != null) {
            $j["message"] = $event->_message;
        }
        $message = json_encode($j, JSON_UNESCAPED_UNICODE) . "\n";

        fwrite($this->_fd, $message);
    }
}
