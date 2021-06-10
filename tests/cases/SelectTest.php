<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class SelectTest extends AbstractCases
{
    private $type = 'select';

    public function testAll()
    {
        $sql = 'SELECT * FROM members';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "all");
        $this->assertEquals($expected, $actual);
    }

    public function testLimitOffset()
    {
        $sql = 'SELECT * FROM members LIMIT 20, 10';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "limitOffset");
        $this->assertEquals($expected, $actual);
    }

    public function testColumns()
    {
        $sql = 'SELECT name, surname, surname FROM members LIMIT 20, 10';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "columns");
        $this->assertEquals($expected, $actual);
    }

    public function testWhere()
    {
        $sql = 'SELECT * FROM members WHERE id>100 LIMIT 20, 10';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "where");
        $this->assertEquals($expected, $actual);
    }

}
