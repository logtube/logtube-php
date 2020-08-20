<?php


namespace Logtube;


use DateTime;
use Exception;

/**
 * Class Event
 * @package Logtube
 */
class Event
{
    /**
     * @var DateTime
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
     * @var string
     */
    public $_crsrc = "-";

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
     * @throws Exception
     */
    public function __construct()
    {
        $this->_timestamp = new DateTime();
    }

    /**
     * @param string $project
     */
    public function setProject($project)
    {
        if ($project == null) return;
        $this->_project = $project;
    }

    /**
     * @param string $env
     */
    public function setEnv($env)
    {
        if ($env == null) return;
        $this->_env = $env;
    }

    /**
     * @param string $topic
     */
    public function setTopic($topic)
    {
        if ($topic == null) return;
        $this->_topic = $topic;
    }

    /**
     * @param string $crid
     */
    public function setCrid($crid)
    {
        if ($crid == null) return;
        $this->_crid = $crid;
    }


    /**
     * @param $crsrc string
     */
    public function setCrsrc($crsrc)
    {
        if ($crsrc == null) return;
        $this->_crsrc = $crsrc;
    }

    /**
     * @param string $keyword
     */
    public function addKeyword($keyword)
    {
        if (empty($keyword)) {
            return;
        }
        if ($this->_keyword == null) {
            $this->_keyword = $keyword;
        } else {
            $this->_keyword = $this->_keyword . "," . $keyword;
        }
    }

    /**
     * @param string $key
     * @param mixed $val
     */
    public function addExtra($key, $val)
    {
        if ($this->_extra == null) {
            $this->_extra = array();
        }
        $this->_extra[$key] = $val;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->_message = $message;
    }

    /**
     * @param IOutput $output
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

    public function commit()
    {
        $this->submit();
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
     * @param string $key
     * @param mixed $val
     * @return $this
     */
    public function x($key, $val)
    {
        $this->addExtra($key, $val);
        return $this;
    }

    /**
     * @param string $message
     */
    public function msg($message)
    {
        $this->setMessage($message);
        $this->submit();
    }

    /**
     * @param string $format
     * @param mixed ...$args
     */
    public function msgf($format, ...$args)
    {
        $this->setMessage(sprintf($format, ...$args));
        $this->submit();
    }
}
