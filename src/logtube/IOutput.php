<?php


namespace Logtube;

/**
 * Interface IOutput
 * @package Logtube
 */
interface IOutput
{
    /**
     * append event to this output
     *
     * @param $event
     * @return void
     */
    function append($event);
}