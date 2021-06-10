<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use PHPUnit\Framework\TestCase;

class AbstractCases extends TestCase
{
    protected $options = array('facade' => 'DB::');

    public function getExpectedValue($type, $fn)
    {
        $file_path = dirname(__FILE__);
        $file_path .= "\\..\\expected\\";
        $file_path .= $type;
        $file_path .= "\\";
        $file_path .= $fn . ".txt";
        return file_get_contents($file_path);
    }
}