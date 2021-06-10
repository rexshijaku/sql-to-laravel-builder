<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class DeleteTest extends AbstractCases
{
    private $type = 'delete';

    public function testAll()
    {
        $sql = "DELETE FROM members";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "all");
        $this->assertEquals($expected, $actual);
    }

    public function testNegation()
    {
        $sql = "DELETE FROM members WHERE NOT (age>10 AND salary > 2000 OR age>9) OR NOT (age < 10)";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "negation");
        $this->assertEquals($expected, $actual);
    }

    public function testWhere()
    {
        $sql = "DELETE FROM members WHERE age>10";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "where");
        $this->assertEquals($expected, $actual);
    }

    public function testOrWhere()
    {
        $sql = "DELETE FROM members WHERE age>10 OR salary > 2000";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "orWhere");
        $this->assertEquals($expected, $actual);
    }
}