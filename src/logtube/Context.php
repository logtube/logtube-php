<?php


namespace Logtube;

use Exception;
use Logtube\Output\FIFODirOutput;
use Logtube\Output\FileOutput;
use Logtube\Output\SingleOutput;
use Logtube\Output\LogSockOutput;

/**
 * Class Context
 * @package Logtube
 */
class Context implements IOutput
{
    /**
     * @var string project
     */
    private $_project;

    /**
     * @var string env
     */
    private $_env;

    /**
     * @var string crid
     */
    private $_crid;

    /**
     * @var string
     */
    private $_crsrc;

    /**
     * @var array
     */
    private $_outputs = [];

    /**
     * @var array
     */
    private $_keywords = [];

    /**
     * Context constructor.
     * @param array $opts
     * @throws Exception
     */
    public function __construct($opts)
    {
        $this->_env = $opts["env"];
        $this->_project = $opts["project"];
        $this->_crid = $opts["crid"];
        $this->_crsrc = $opts["crsrc"];
        if (isset($opts["file"]) && !empty($opts["file"])) {
            array_push($this->_outputs, new FileOutput($opts["file"]));
        }
        if (isset($opts["logsock"]) && !empty($opts["logsock"])) {
            array_push($this->_outputs, new LogSockOutput($opts["logsock"]));
        } else if (isset($opts["fifodir"]) && !empty($opts["fifodir"]) && file_exists($opts["fifodir"])) {
            array_push($this->_outputs, new FIFODirOutput(
                $opts["fifodir"],
                isset($opts["fifodir_nb"]) && !empty($opts["fifodir_nb"])
            ));
        } else if (isset($opts["single"]) && !empty($opts["single"])) {
            array_push($this->_outputs, new SingleOutput($opts["single"]));
        }
    }

    /**
     * @return string
     */
    public function project()
    {
        return $this->_project;
    }

    /**
     * @return string
     */
    public function env()
    {
        return $this->_env;
    }

    /**
     * @return string
     */
    public function crid()
    {
        return $this->_crid;
    }

    /**
     * @return mixed|string
     */
    public function crsrc()
    {
        return $this->_crsrc;
    }

    /**
     * add default keywords to all events
     *
     * @param string ...$keyword keyword to add
     */
    public function addDefaultKeyword(...$keyword)
    {
        array_push($this->_keywords, ...$keyword);
    }

    /**
     * clear default keywords
     */
    public function clearDefaultKeywords()
    {
        $this->_keywords = [];
    }

    /**
     * @param $topic string
     * @return Event
     * @throws Exception
     */
    public function event($topic)
    {
        if ($topic == null) {
            $topic = "noname";
        }
        $e = new Event();
        $e->setProject($this->project());
        $e->setEnv($this->env());
        $e->setCrid($this->crid());
        $e->setCrsrc($this->crsrc());
        $e->setTopic($topic);
        $e->setOutput($this);
        foreach ($this->_keywords as $k) {
            $e->addKeyword($k);
        }
        return $e;
    }

    /**
     * @param Event $event
     */
    public function append($event)
    {
        foreach ($this->_outputs as $i => $output) {
            /** @var $output IOutput */
            $output->append($event);
        }
    }
}
