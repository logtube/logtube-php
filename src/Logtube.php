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
     * extract crid from request
     * 
     * @return string
     * @throws Exception
     */
    private static function extractCrid()
    {
        return empty($_GET["_crid"])
            ?
            (empty($_SERVER["HTTP_X_CORRELATION_ID"])
                ?
                bin2hex(random_bytes(8))
                :
                $_SERVER["HTTP_X_CORRELATION_ID"])
            :
            $_GET["_crid"];
    }

    /**
     * initialize the Logtube
     * 
     * @param null|array $opts
     * @throws Exception
     */
    public static function setup($opts)
    {
        if (empty($opts)) {
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
     * get the internal context
     * 
     * @return Context
     */
    public static function context()
    {
        return self::$_context;
    }

    /**
     * get the project name
     * 
     * @return string
     */
    public static function project()
    {
        return self::context()->project();
    }

    /**
     * get the environment name
     * 
     * @return string
     */
    public static function env()
    {
        return self::context()->env();
    }

    /**
     * get crid
     * 
     * @return string
     */
    public static function crid()
    {
        return self::context()->crid();
    }

    /**
     * add default keywords
     * 
     * @var string ...$keyword
     */
    public static function addDefaultKeyword(...$keyword)
    {
        self::context()->addDefaultKeyword(...$keyword);
    }

    /**
     * clear default keywords
     */
    public static function clearDefaultKeywords()
    {
        self::context()->clearDefaultKeywords();
    }

    /**
     * create a log event with given topic
     * 
     * @param string $topic
     * @return \Logtube\Event
     */
    public static function event($topic)
    {
        return self::context()->event($topic);
    }

    /**
     * create and commit a plain text log event
     * 
     * @param string $topic
     * @param array|string|null $keyword
     * @param string $format
     * @param mixed ...$args
     */
    public static function log($topic, $keyword, $format, ...$args)
    {
        if (empty($keyword)) {
            $keyword = [];
        }
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
     * @param string|array|null $keyword
     * @param string $format
     * @param mixed ...$args
     */
    public static function info($keyword, $format, ...$args)
    {
        self::log("info", $keyword, $format, ...$args);
    }

    /**
     * @param string|array|null $keyword
     * @param string $format
     * @param mixed ...$args
     */
    public static function warn($keyword, $format, ...$args)
    {
        self::log("warn", $keyword, $format, ...$args);
    }

    /**
     * @param string|array|null $keyword
     * @param string $format
     * @param mixed ...$args
     */
    public static function error($keyword, $format, ...$args)
    {
        self::log("err", $keyword, $format, ...$args);
    }

    /**
     * @param string|array|null $keyword
     * @param string $format
     * @param mixed ...$args
     */
    public static function debug($keyword, $format, ...$args)
    {
        self::log("debug", $keyword, $format, ...$args);
    }
}

// initialize a dummy context
Logtube::setup(null);

/**
 * @param string|array|null $keyword
 * @param string $format
 * @param mixed ...$args
 */
function ILog($keyword, $format, ...$args)
{
    Logtube::info($keyword, $format, ...$args);
}

/**
 * @param string|array|null $keyword
 * @param string $format
 * @param mixed ...$args
 */
function WLog($keyword, $format, ...$args)
{
    Logtube::warn($keyword, $format, ...$args);
}

/**
 * @param string|array|null $keyword
 * @param string $format
 * @param mixed ...$args
 */
function ELog($keyword, $format, ...$args)
{
    Logtube::error($keyword, $format, ...$args);
}

/**
 * @param string|array|null $keyword
 * @param string $format
 * @param mixed ...$args
 */
function DLog($keyword, $format, ...$args)
{
    Logtube::debug($keyword, $format, ...$args);
}