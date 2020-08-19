<?php


namespace Logtube\Committer;


use Logtube\Event;

class AuditCommitter
{

    /**
     * @var Event
     */
    private $_event;

    public function __construct($event)
    {
        $this->_event = $event;
    }

    /**
     * @param $user_code string
     * @return $this
     */
    public function setUserCode($user_code)
    {
        $this->_event->x("user_code", $user_code);
        return $this;
    }

    /**
     * @param $ip string
     * @return $this
     */
    public function setIP($ip)
    {
        $this->_event->x("ip", $ip);
        return $this;
    }

    /**
     * @param $user_name
     * @return $this
     */
    public function setUserName($user_name)
    {
        $this->_event->x("user_name", $user_name);
        return $this;
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
     * @param $url string
     * @return $this
     */
    public function setURL($url)
    {
        $this->_event->x("url", $url);
        return $this;
    }

    public function commit()
    {
        $this->_event->commit();
    }

    public function submit()
    {
        $this->commit();
    }

}