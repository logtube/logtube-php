<?php

use Logtube\Context;

Context::createCurrent();

/**
 * @param $opts
 * @throws Exception
 */
function logtube_setup($opts)
{
    Context::current()->setup($opts);
}

function logtube_crid()
{
    return Context::current()->crid();
}

function logtube_project()
{
    return Context::current()->project();
}

function logtube_env()
{
    return Context::current()->env();
}

function logtube_event($topic)
{
    return Context::current()->event($topic);
}

function logtube_log($topic, $keyword, $format, ...$args)
{
    if (!is_array($keyword)) {
        $keyword = array($keyword);
    }
    if (sizeof($args) == 0) {
        logtube_event($topic)->k(...$keyword)->msg($format);
    } else {
        logtube_event($topic)->event($topic)->k(...$keyword)->msgf($format, ...$args);
    }
}

function ilog($keyword, $format, ...$args)
{
    logtube_log("info", $keyword, $format, ...$args);
}

function elog($keyword, $format, ... $args)
{
    logtube_log("err", $keyword, $format, ...$args);
}

function dlog($keyword, $format, ... $args)
{
    logtube_log("debug", $keyword, $format, ...$args);
}

function wlog($keyword, $format, ...$args)
{
    logtube_log("warn", $keyword, $format, ...$args);
}