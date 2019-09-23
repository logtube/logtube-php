<?php


namespace Logtube;

use Logtube\Output\FileOutput;

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
     * @var array
     */
    private $_outputs = [];

    /**
     * Context constructor.
     * @param $opts array
     * @throws \Exception
     */
    public function __construct($opts)
    {
        $this->_env = $opts["env"];
        $this->_project = $opts["project"];
        $this->_crid = $opts["crid"];
        if (!empty($opts["file"])) {
            array_push($this->_outputs, new FileOutput($opts["file"]));
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
     * @param $topic string
     * @return Event
     * @throws \Exception
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
        $e->setTopic($topic);
        $e->setOutput($this);
        return $e;
    }

    /**
     * @param $event Event
     */
    public function append($event)
    {
        foreach ($this->_outputs as $i => $output) {
            /** @var $output IOutput */
            $output->append($event);
        }
    }
}