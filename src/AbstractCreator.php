<?php


namespace RexShijaku\SQLToLaravelBuilder;

/**
 * This class provides additional functionality for the Creator class.
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class AbstractCreator
{
    public $qb;
    public $lastly;
    public $qb_closed;
    public $in_union = false;


    function isSingleTable($parsed)
    {
        if (isset($parsed['FROM'])) {
            if (count($parsed['FROM']) == 1)
                return $parsed['FROM'][0]['expr_type'] == 'table';
        }
        return false;
    }

    public function resetQ()
    {
        $this->qb = '';
        $this->lastly = '';
    }

}