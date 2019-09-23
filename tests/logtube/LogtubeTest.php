<?php


use PHPUnit\Framework\TestCase;

class LogtubeTest extends TestCase
{

    public function testLogtube()
    {
        Logtube::setup([
            "project" => "testcase",
            "env" => "test",
            "file" => [
                "dir" => "logs",
                "subdirs" => [
                    "err" => "important",
                    "warn" => "important",
                ]
            ]
        ]);

        ILog("hello,world", "hello %s", "world");
        ELog("hello,world", "hello %s", "world");
        WLog("hello,world", "hello %s", "world");
        DLog("hello,world", "hello %s", "world");


        Logtube::setup([
            "project" => "testcase",
            "env" => "test",
            "file" => [
                "dir" => "logs",
            ]
        ]);

        ILog("hello,world", "hello %s", "world");
        ELog("hello,world", "hello %s", "world");
        WLog("hello,world", "hello %s", "world");
        DLog("hello,world", "hello %s", "world");

        Logtube::setup([
            "file" => [
                "dir" => "logs",
            ]
        ]);

        ILog("hello,world", "hello %s", "world");
        ELog("hello,world", "hello %s", "world");
        WLog("hello,world", "hello %s", "world");
        DLog("hello,world", "hello %s", "world");

        $this->assertTrue(true);
    }

}
