<?php


namespace Logtube\Output;

use Logtube\Event;
use Logtube\IOutput;

class FileOutput implements IOutput
{
    /**
     * @var string
     */
    private $_dir;

    /**
     * @var array
     */
    private $_subdirs = [];

    /**
     * @var array
     */
    private $_fds = [];

    /**
     * @param $filename string
     */
    public static function createDirIfNotExisted($filename)
    {
        if (!file_exists($filename)) {
            mkdir($filename, 0755, true);
        }
    }

    /**
     * FileOutput constructor.
     * @param $opts array
     */
    public function __construct($opts)
    {
        $this->_dir = $opts["dir"];
        if (!empty($opts["subdirs"])) {
            $this->_subdirs = $opts["subdirs"];
        }
        $this->createDirs();
    }

    /**
     * @return void
     */
    private function createDirs()
    {
        // create dir
        self::createDirIfNotExisted($this->_dir);
        // deduplicated subdirs
        $subdirs = [];
        foreach ($this->_subdirs as $key => $val) {
            $subdirs[$val] = true;
        }
        // create subdirs
        foreach ($subdirs as $key => $val) {
            self::createDirIfNotExisted($this->_dir . DIRECTORY_SEPARATOR . $key);
        }
    }

    /**
     * @param $event Event
     * @return resource
     */
    private function fd($event)
    {
        $filename = $this->_dir . DIRECTORY_SEPARATOR;
        if (!empty($this->_subdirs[$event->_topic])) {
            $filename = $filename . DIRECTORY_SEPARATOR . $this->_subdirs[$event->_topic];
        }
        $filename = $filename . DIRECTORY_SEPARATOR . $event->_env . "." . $event->_topic . "." . $event->_project . "." . $event->_timestamp->format("Y-m-d") . ".log";
        if (empty($this->_fds[$filename])) {
            $this->_fds[$filename] = fopen($filename, "a");
        }
        return $this->_fds[$filename];
    }

    /**
     * append event to this output
     *
     * @param $event Event
     * @return void
     */
    function append($event)
    {
        $fd = $this->fd($event);
        $line = "[" . $event->_timestamp->format("Y-m-d H:i:s.v O") . "] ";
        $struct = ["c" => $event->_crid];
        if ($event->_keyword != null) {
            $struct["k"] = $event->_keyword;
        }
        if ($event->_extra != null) {
            $struct["x"] = $event->_extra;
        }
        $line = $line . json_encode([$struct]);
        if ($event->_message != null) {
            $line = $line . " " . $event->_message;
        }
        fputs($fd, $line . "\n");
    }
}