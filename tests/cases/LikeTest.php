<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class LikeTest extends AbstractCases
{
    private $type = 'like';

    public function testLike()
    {
        $sql = "SELECT * FROM members WHERE name LIKE '%j%' AND  surname LIKE 'j%' or title not LIKE '%gui'";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "like");
        $this->assertEquals($expected, $actual);
    }
}