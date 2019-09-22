<?php


namespace Logtube;


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
     * @var string
     */
    public $_keyword = null;

    /**
     * @var string
     */
    public $_message = null;

    /**
     * @var null|array
     */
    public $_extra = null;

    /**
     * @var null|\Closure
     */
    public $_submit = null;

    public function __construct()
    {
        $this->_timestamp = new \DateTime();
    }

    public function setProject($project)
    {
        if ($project == null) return;
        $this->_project = $project;
    }

    public function setEnv($env)
    {
        if ($env == null) return;
        $this->_env = $env;
    }

    public function setTopic($topic)
    {
        if ($topic == null) return;
        $this->_topic = $topic;
    }

    public function setCrid($crid)
    {
        if ($crid == null) return;
        $this->_crid = $crid;
    }

    public function addKeyword($keyword)
    {
        if ($this->_keyword == null) {
            $this->_keyword = $keyword;
        } else {
            $this->_keyword = $this->_keyword . "," . $keyword;
        }
    }

    public function addExtra($key, $val)
    {
        if ($this->_extra == null) {
            $this->_extra = array();
        }
        $this->_extra[$key] = $val;
    }

    public function setMessage($message)
    {
        $this->_message = $message;
    }

    public function setSubmit($submit)
    {
        $this->_submit = $submit;
    }

    public function submit()
    {
        $submit = $this->_submit;
        if ($submit == null) {
            return;
        }
        $submit($this);
    }

    /////////////////////// chain messages

    public function k(...$keyword)
    {
        foreach ($keyword as $item) {
            $this->addKeyword($item);
        }
        return $this;
    }

    public function x($key, $val)
    {
        $this->addExtra($key, $val);
        return $this;
    }

    public function msg($message)
    {
        $this->setMessage($message);
        $this->submit();
    }

    public function msgf($format, ...$args)
    {
        $this->setMessage(sprintf($format, ...$args));
        $this->submit();
    }

}
