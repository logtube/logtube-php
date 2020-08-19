<?php


namespace Logtube\Committer;


use Logtube\Event;

class PerfCommitter
{
    /**
     * @var Event
     */
    private $_event;

    private $_start_time;

    public function __construct($event)
    {
        $this->_event = $event;
        $this->_start_time = intval(microtime(true) * 1000);
    }

    /**
     * @param $action string
     * @return $this
     */
    public function setAction($action)
    {
        $this->_event->x("action", $action);
        return $this;
    }

    /**
     * @param $action_detail string
     * @return $this
     */
    public function setActionDetail($action_detail)
    {
        $this->_event->x("action_detail", $action_detail);
        return $this;
    }

    /**
     * @param $value_integer int
     * @return $this
     */
    public function setValueInteger($value_integer)
    {
        $this->_event->x("value_integer", $value_integer);
        return $this;
    }

    public function commit()
    {
        $this->_event->x("duration", intval(microtime(true) * 1000) - $this->_start_time)->commit();
    }

    public function submit()
    {
        $this->commit();
    }

}