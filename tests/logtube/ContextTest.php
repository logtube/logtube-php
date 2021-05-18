<?php


use Logtube\Context;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{

    public function testContextSetup()
    {
        $opts = array(
            "project" => "testcase",
            "env" => "test",
            "crid" => "xxxxxx",
            "crsrc" => "-",
            "file" => array(
                "dir" => "logs",
                "subdirs" => array(
                    "info" => "important",
                    "err" => "important",
                )
            )
        );

        $ctx = new Context($opts);
        $ctx->addDefaultKeyword("dft-keyword");

        $this->assertEquals("testcase", $ctx->project());
        $this->assertEquals("test", $ctx->env());
        $this->assertNotNull($ctx->crid());

        $e = $ctx->event("info");
        $this->assertEquals("testcase", $e->_project);
        $this->assertEquals("test", $e->_env);
        $this->assertNotNull($e->_crid);

        $e->k("hello", "world")->x("key1", "val1")->msgf("hello, world");
    }

}
