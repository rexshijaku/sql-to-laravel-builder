<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class LimitTest extends AbstractCases
{
    private $type = 'limit';

    public function testLimit()
    {
        $sql = "SELECT * FROM members LIMIT 1";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "limit");
        $this->assertEquals($expected, $actual);
    }

    public function testLimitAndOffset()
    {
        $sql = "SELECT * FROM members LIMIT 2,1";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "limitOffset");
        $this->assertEquals($expected, $actual);
    }
}