<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class UnionTest extends AbstractCases
{
    private $type = 'union';

    public function testUnion()
    {
        $sql = "SELECT * FROM members WHERE age > 25 UNION SELECT * FROM members WHERE name like '%ius%'";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "union");
        $this->assertEquals($expected, $actual);
    }
}