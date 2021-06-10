<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class GroupByTest extends AbstractCases
{
    private $type = 'group_by';

    public function testGroupBy()
    {
        $sql = "SELECT age,salary,count(*) FROM members GROUP BY age, salary";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "by");
        $this->assertEquals($expected, $actual);
    }

    public function testGroupByFunction()
    {
        $sql = "SELECT age, some_function(),count(*) FROM members GROUP BY age, some_function()";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "byFunction");
        $this->assertEquals($expected, $actual);
    }

}