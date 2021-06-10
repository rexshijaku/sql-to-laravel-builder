<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class CountTest extends AbstractCases
{
    private $type = 'count';

    public function testAvg()
    {
        $sql = "SELECT COUNT(*) FROM members";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "count");
        $this->assertEquals($expected, $actual);
    }

    // todo add more, with alias, column and column alias
}