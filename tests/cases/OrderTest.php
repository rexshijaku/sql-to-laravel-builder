<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class OrderTest extends AbstractCases
{
    private $type = 'order';

    public function testAsc()
    {
        $sql = "SELECT * FROM members ORDER BY name DESC, surname ASC";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "asc");
        $this->assertEquals($expected, $actual);
    }

    public function testByFunction()
    {
        $sql = "SELECT * FROM members ORDER BY RAND() ASC";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "byFunction");
        $this->assertEquals($expected, $actual);
    }
}