<?php


namespace Logtube\Output;

use Logtube\Event;
use Logtube\IOutput;
use Exception;

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
     * @var int 写入文件的均衡数量。
     *
     * 日志文件均衡地写入 xxx.1.log, xxx.2.log, ... xxx.$_balance.log
     * 默认这个值等于 5
     */
    private $_balance;

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
        $this->_balance = isset($opts["balance"]) ? $opts["balance"] : 5;
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
     * @param Event $event
     * @param int $selectedBalance
     * @return string
     */
    private function logfile($event, $selectedBalance)
    {
        $filename = $this->_dir . DIRECTORY_SEPARATOR;
        if (!empty($this->_subdirs[$event->_topic])) {
            $filename = $filename . DIRECTORY_SEPARATOR . $this->_subdirs[$event->_topic];
        }
        return $filename . DIRECTORY_SEPARATOR . $event->_env . "." . $event->_topic . "." . $event->_project . "." . $event->_timestamp->format("Y-m-d") . ".$selectedBalance.log";
    }

    /**
     * 写入消息到文件
     *
     * @param string $message
     * @param Event $event
     * @return void
     * @throws Exception 当文件写入失败的时候抛出异常。
     */
    private function writeLog($message, $event)
    {
        if ($this->_balance <= 1) {
            $fileName = $this->logfile($event, 1);
            $result = file_put_contents($fileName, $message . "\n", FILE_APPEND | LOCK_EX);
        } else {
            for ($i = 0; $i < 3; $i++) {
                $fileName = $this->logfile($event, mt_rand(1, $this->_balance));
                $result = file_put_contents($fileName, $message . "\n", FILE_APPEND | LOCK_EX);
                if (false !== $result) {
                    break;
                }
            }
        }
        if (false === $result) {
            throw new Exception("[logtube] Failed to write to file \"$fileName\".");
        }
    }

    /**
     * append event to this output
     *
     * @param $event Event
     * @return void
     */
    function append($event)
    {
        $line = "[" . $event->_timestamp->format("Y-m-d H:i:s.v O") . "] ";
        $struct = ["c" => $event->_crid, "s" => $event->_crsrc];
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
        $this->writeLog($line, $event);
    }
}
