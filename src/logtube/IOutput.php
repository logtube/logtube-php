<?php


namespace Logtube;

/**
 * Interface IOutput
 * @package Logtube
 */
interface IOutput
{

    /**
     * @param $event Event
     * @return void
     */
    function append($event);

}