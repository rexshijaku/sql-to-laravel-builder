<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class DistinctTest extends AbstractCases
{
    private $type = 'distinct';

    public function testAll()
    {
        $sql = "SELECT DISTINCT * FROM members";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "all");
        $this->assertEquals($expected, $actual);
    }
}