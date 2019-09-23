<?php


namespace Logtube;


/**
 * Class Event
 * @package Logtube
 */
class Event
{
    /**
     * @var \DateTime
     */
    public $_timestamp;

    /**
     * @var string
     */
    public $_project = "noname";

    /**
     * @var string
     */
    public $_env = "noname";

    /**
     * @var string
     */
    public $_topic = "noname";

    /**
     * @var string
     */
    public $_crid = "-";

    /**
     * @var null|string
     */
    public $_keyword = null;

    /**
     * @var null|string
     */
    public $_message = null;

    /**
     * @var null|array
     */
    public $_extra = null;

    /**
     * @var null|IOutput
     */
    private $_output = null;

    /**
     * Event constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->_timestamp = new \DateTime();
    }

    /**
     * @param $project string
     */
    public function setProject($project)
    {
        if ($project == null) return;
        $this->_project = $project;
    }

    /**
     * @param $env string
     */
    public function setEnv($env)
    {
        if ($env == null) return;
        $this->_env = $env;
    }

    /**
     * @param $topic string
     */
    public function setTopic($topic)
    {
        if ($topic == null) return;
        $this->_topic = $topic;
    }

    /**
     * @param $crid string
     */
    public function setCrid($crid)
    {
        if ($crid == null) return;
        $this->_crid = $crid;
    }

    /**
     * @param $keyword string
     */
    public function addKeyword($keyword)
    {
        if ($this->_keyword == null) {
            $this->_keyword = $keyword;
        } else {
            $this->_keyword = $this->_keyword . "," . $keyword;
        }
    }

    /**
     * @param $key string
     * @param $val mixed
     */
    public function addExtra($key, $val)
    {
        if ($this->_extra == null) {
            $this->_extra = array();
        }
        $this->_extra[$key] = $val;
    }

    /**
     * @param $message string
     */
    public function setMessage($message)
    {
        $this->_message = $message;
    }

    /**
     * @param $output IOutput
     */
    public function setOutput($output)
    {
        $this->_output = $output;
    }

    /**
     * @return void
     */
    public function submit()
    {
        if ($this->_output != null) {
            $this->_output->append($this);
        }
    }

    /////////////////////// chain messages

    /**
     * @param mixed ...$keyword
     * @return $this
     */
    public function k(...$keyword)
    {
        foreach ($keyword as $item) {
            $this->addKeyword($item);
        }
        return $this;
    }

    /**
     * @param $key string
     * @param $val mixed
     * @return $this
     */
    public function x($key, $val)
    {
        $this->addExtra($key, $val);
        return $this;
    }

    /**
     * @param $message string
     */
    public function msg($message)
    {
        $this->setMessage($message);
        $this->submit();
    }

    /**
     * @param $format string
     * @param mixed ...$args
     */
    public function msgf($format, ...$args)
    {
        $this->setMessage(sprintf($format, ...$args));
        $this->submit();
    }

}
