<?php

namespace Logtube\Committer;

use Logtube\Event;

class JobCommitter
{


    /**
     * @var Event
     */
    private $_event;

    private $_started_at;

    public function __construct($event)
    {
        $this->_event = $event;
        $this->_started_at = 0;
    }

    /**
     * @param $jobName string
     * @return $this
     */
    public function setJobName($jobName)
    {
        $this->_event->x("job_name", $jobName);
        return $this;
    }

    /**
     * @param $jobId string
     * @return $this
     */
    public function setJobId($jobId)
    {
        $this->_event->x("job_id", $jobId);
        return $this;
    }

    /**
     * @param $keyword string
     * @return $this
     */
    public function addKeyword($keyword)
    {
        $this->_event->addKeyword($keyword);
        return $this;
    }

    /**
     * @param $key string
     * @param $value
     * @return $this
     */
    public function addExtra($key, $value)
    {
        $this->_event->x($key, $value);
        return $this;
    }

    /**
     * @return $this
     */
    public function markStart()
    {
        $this->_started_at = intval(microtime(true) * 1000);
        $this->_event->x("started_at", $this->_started_at);
        $event = clone $this->_event;
        $event->x("result", "started");
        $event->commit();
        return $this;
    }

    /**
     * @param $success boolean
     * @param $message string
     * @return $this
     */
    public function setResult($success, $message)
    {
        if ($success) {
            $this->_event->x("result", "ok");
        } else {
            $this->_event->x("result", "failed");
        }
        $this->_event->setMessage($message);
        return $this;
    }

    /**
     * @return $this
     */
    public function markEnd()
    {
        $ended_at = intval(microtime(true) * 1000);
        $this->_event->x("duration", $ended_at - $this->_started_at);
        $this->_event->x("ended_at", $ended_at);
        $this->_event->commit();
        return $this;
    }

}