<?php

use Logtube\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{

    public function testEventCreation()
    {
        $event = new Event();
        $event->setProject("testcase");
        $event->setCrid("crid");
        $event->setEnv("test");
        $event->setTopic("test");
        $event->addKeyword("hello");
        $event->addKeyword("world");
        $event->msgf("hello %s", "world");
        $this->assertEquals("testcase", $event->_project);
        $this->assertEquals("test", $event->_env);
        $this->assertEquals("crid", $event->_crid);
        $this->assertEquals("test", $event->_topic);
        $this->assertEquals("hello,world", $event->_keyword);
        $this->assertEquals("hello world", $event->_message);
    }

}