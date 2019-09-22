<?php


namespace Logtube;

class FileOutput implements IOutput
{
    private $_dir;
    private $_subdirs;
    private $_fds = [];

    public function __construct($opts)
    {
        $this->_dir = $opts["dir"];
        $this->_subdirs = $opts["subdirs"];
    }

    public function createDir($filename)
    {
        if (!file_exists($filename)) {
            @mkdir($filename, 0755, true);
        }
    }

    public function createDirs()
    {
        $this->createDir($this->_dir);
        $subdirs = [];
        foreach ($this->_subdirs as $key => $val) {
            $subdirs[$val] = true;
        }
        foreach ($subdirs as $key => $val) {
            $this->createDir($this->_dir . DIRECTORY_SEPARATOR . $key);
        }
    }

    /**
     * @param $event Event
     * @return resource
     */
    private function fd($event)
    {
        $filename = $this->_dir . DIRECTORY_SEPARATOR;
        if (isset($this->_subdirs[$event->_topic])) {
            $filename = $filename . DIRECTORY_SEPARATOR . $this->_subdirs[$event->_topic];
        }
        $filename = $filename . DIRECTORY_SEPARATOR . $event->_env . "." . $event->_topic . "." . $event->_project . "." . $event->_timestamp->format("Y-m-d") . ".log";
        if (!isset($this->_fds[$filename])) {
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