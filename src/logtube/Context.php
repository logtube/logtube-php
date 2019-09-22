<?php


namespace Logtube;


class Context
{
    /**
     * @var Context current context
     */
    private static $_current;

    /**
     * @var string project
     */
    private $_project = "noname";

    /**
     * @var string env
     */
    private $_env = "noname";

    /**
     * @var string crid
     */
    private $_crid = "-";

    /**
     * @var array
     */
    private $_outputs = array();

    public static function createCurrent()
    {
        if (Context::$_current == null) {
            Context::$_current = new Context();
        }
    }

    /**
     * @return Context
     */
    public static function current()
    {
        return Context::$_current;
    }

    /**
     * @param $opts array
     * @throws \Exception
     */
    public function setup($opts)
    {
        $this->_env = isset($opts["env"]) ? $opts["env"] : "noname";
        $this->_project = isset($opts["project"]) ? $opts["project"] : "noname";
        $this->_crid = isset($_GET["_crid"]) ?
            $_GET["_crid"] : (isset($_SERVER["HTTP_X_CORRELATION_ID"]) ?
                $_SERVER["HTTP_X_CORRELATION_ID"] :
                bin2hex(random_bytes(8)
                )
            );
        $this->_outputs = [];
        if (isset($opts["file"])) {
            $fo = new FileOutput($opts["file"]);
            $fo->createDirs();
            array_push($this->_outputs, $fo);
        }
    }

    /**
     * @return string
     */
    public function project()
    {
        return $this->_project;
    }

    /**
     * @return string
     */
    public function env()
    {
        return $this->_env;
    }

    /**
     * @return string
     */
    public function crid()
    {
        return $this->_crid;
    }

    public function event($topic)
    {
        if ($topic == null) {
            $topic = "info";
        }
        $e = new Event();
        $e->setProject($this->project());
        $e->setEnv($this->env());
        $e->setCrid($this->crid());
        $e->setTopic($topic);
        $e->setSubmit(function ($event) {
            $this->submit($event);
        });
        return $e;
    }

    /**
     * @param $event Event
     */
    public function submit($event)
    {
        foreach ($this->_outputs as $i => $output) {
            /** @var $output IOutput */
            $output->append($event);
        }
    }
}