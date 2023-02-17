<?php


use PHPUnit\Framework\TestCase;

class LogtubeTest extends TestCase
{

    public function testLogtubeSingle()
    {
        Logtube::setup([
            "project" => "testcase",
            "env" => "test",
            "single" => "single.log",
        ]);

        Logtube::addDefaultKeyword("dft-keyword1", "dft-keyword2");

        ILog("", "hello %s", "world");
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

        $committer = Logtube::audit();
        $committer->setUserCode("20200202")
            ->setUserName("测试员")
            ->setAction("提起撤销")
            ->setActionDetail("用户  aaaa");
        $committer->submit();

        $committer = Logtube::perf();
        $committer->setAction("提起撤销")
            ->setActionDetail("用户  aaaa");
        sleep(1);
        $committer->submit();

        $committer = Logtube::job();
        $committer->setJobName("sleep_1s_job")
            ->setJobId("jobxxxxxx-xxxxx-xxxxx")
            ->addKeyword("sleep")
            ->markStart();

        // do something

        $committer->setResult(true, "sleep well")
            ->markEnd();

        $this->assertTrue(true);
    }


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

        Logtube::addDefaultKeyword("dft-keyword1", "dft-keyword2");

        ILog("", "hello %s", "world");
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

        $committer = Logtube::audit();
        $committer->setUserCode("20200202")
            ->setUserName("测试员")
            ->setAction("提起撤销")
            ->setActionDetail("用户  aaaa");
        $committer->submit();

        $committer = Logtube::perf();
        $committer->setAction("提起撤销")
            ->setActionDetail("用户  aaaa");
        sleep(1);
        $committer->submit();

        $committer = Logtube::job();
        $committer->setJobName("sleep_1s_job")
            ->setJobId("jobxxxxxx-xxxxx-xxxxx")
            ->addKeyword("sleep")
            ->markStart();

        // do something

        $committer->setResult(true, "sleep well")
            ->markEnd();

        $this->assertTrue(true);
    }

}
