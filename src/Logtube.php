<?php

use Logtube\Context;

/**
 * Class Logtube
 */
class Logtube
{
    /**
     * @var Context
     */
    private static $_context;

    /**
     * @return string
     * @throws Exception
     */
    private static function extractCrid()
    {
        return empty($_GET["_crid"])
            ?
            (
            empty($_SERVER["HTTP_X_CORRELATION_ID"])
                ?
                bin2hex(random_bytes(8))
                :
                $_SERVER["HTTP_X_CORRELATION_ID"]
            )
            :
            $_GET["_crid"];
    }

    /**
     * @param $opts null|array
     * @throws Exception
     */
    public static function setup($opts)
    {
        if ($opts == null) {
            self::$_context = new Context([
                "project" => "noname",
                "env" => "noname",
                "crid" => "-",
            ]);
            return;
        }
        self::$_context = new Context([
            "project" => empty($opts["project"]) ? "noname" : $opts["project"],
            "env" => empty($opts["env"]) ? "noname" : $opts["env"],
            "crid" => self::extractCrid(),
            "file" => empty($opts["file"]) ? null : $opts["file"],
        ]);
    }

    /**
     * @return Context
     */
    public static function context()
    {
        return self::$_context;
    }

    /**
     * @return string
     */
    public static function project()
    {
        return self::context()->project();
    }

    /**
     * @return string
     */
    public static function env()
    {
        return self::context()->env();
    }

    /**
     * @return string
     */
    public static function crid()
    {
        return self::context()->crid();
    }

    /**
     * @param $topic string
     * @return \Logtube\Event
     * @throws \Exception
     */
    public static function event($topic)
    {
        return self::context()->event($topic);
    }

    /**
     * @param $topic string
     * @param $keyword array|string
     * @param $format string
     * @param mixed ...$args
     *
     * @throws \Exception
     */
    public static function log($topic, $keyword, $format, ...$args)
    {
        // wrap $keyword as array
        if (!is_array($keyword)) {
            $keyword = array($keyword);
        }
        if (sizeof($args) == 0) {
            self::event($topic)->k(...$keyword)->msg($format);
        } else {
            self::event($topic)->k(...$keyword)->msgf($format, ...$args);
        }
    }

    /**
     * @param $keyword
     * @param $format
     * @param mixed ...$args
     * @throws Exception
     */
    public static function info($keyword, $format, ...$args)
    {
        self::log("info", $keyword, $format, ...$args);
    }

    /**
     * @param $keyword
     * @param $format
     * @param mixed ...$args
     * @throws Exception
     */
    public static function warn($keyword, $format, ...$args)
    {
        self::log("warn", $keyword, $format, ...$args);
    }

    /**
     * @param $keyword
     * @param $format
     * @param mixed ...$args
     * @throws Exception
     */
    public static function error($keyword, $format, ...$args)
    {
        self::log("err", $keyword, $format, ...$args);
    }

    /**
     * @param $keyword
     * @param $format
     * @param mixed ...$args
     * @throws Exception
     */
    public static function debug($keyword, $format, ...$args)
    {
        self::log("debug", $keyword, $format, ...$args);
    }

}

Logtube::setup(null);

function ILog($keyword, $format, ...$args)
{
    Logtube::log("info", $keyword, $format, ...$args);
}

function WLog($keyword, $format, ...$args)
{
    Logtube::log("warn", $keyword, $format, ...$args);
}

function ELog($keyword, $format, ...$args)
{
    Logtube::log("err", $keyword, $format, ...$args);
}

function DLog($keyword, $format, ...$args)
{
    Logtube::log("debug", $keyword, $format, ...$args);
}
