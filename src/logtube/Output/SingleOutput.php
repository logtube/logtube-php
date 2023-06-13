<?php

namespace Logtube\Output;

use DateTime;
use Logtube\IOutput;

class SingleOutput implements IOutput
{
    /**
     * @var string
     */
    private $_file;

    public function __construct($_file)
    {
        $this->_file = $_file;
    }

    function append($event)
    {
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
        file_put_contents($this->_file, $message, FILE_APPEND);
    }

}