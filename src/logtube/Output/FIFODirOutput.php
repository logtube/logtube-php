<?php

namespace Logtube\Output;

use DateTime;
use Exception;
use Logtube\IOutput;

class FIFODirOutput implements IOutput
{
    private $_file = false;

    public function __construct($_dir)
    {
        $_dir = rtrim($_dir, DIRECTORY_SEPARATOR);

        $files = [];
        try {
            if ($fd = opendir($_dir)) {
                readdir($fd);
                while (false !== ($entry = readdir($fd))) {
                    if ($entry == "." || $entry == "..") {
                        continue;
                    }
                    if ($stat = stat($_dir . DIRECTORY_SEPARATOR . $entry)) {
                        if ($stat['mode'] & 0010000) {
                            array_push($files, $entry);
                        }
                    }
                }
                closedir($fd);
            }
        } catch (Exception $e) {
        }

        if (empty($files)) {
            return;
        }

        $this->_file = $_dir . DIRECTORY_SEPARATOR . $files[array_rand($files)];
    }


    function append($event)
    {
        if (empty($this->_file)) {
            return;
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
        $message = json_encode($j) . "\n";
        file_put_contents($this->_file, $message, FILE_APPEND);
    }

}