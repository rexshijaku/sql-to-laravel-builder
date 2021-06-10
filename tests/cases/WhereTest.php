<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class WhereTest extends AbstractCases
{
    private $type = 'where';

    public function testAtomic()
    {
        $sql = "SELECT * FROM members WHERE age = 25";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "atomic");
        $this->assertEquals($expected, $actual);
    }

    public function testAnd()
    {
        $sql = "SELECT * FROM members WHERE age = 25 AND name = 'David'";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "and");
        $this->assertEquals($expected, $actual);
    }

    public function testColumnOperation()
    {
        $sql = "SELECT * FROM members WHERE age +1  = 25";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "columnOperation");
        $this->assertEquals($expected, $actual);
    }

    public function testColumnsOperation()
    {
        $sql = "SELECT * FROM members WHERE age+0 = age";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "columnsOperation");
        $this->assertEquals($expected, $actual);
    }

    public function testOrLikeOr()
    {
        $sql = "SELECT * FROM members WHERE age+0  = age or name like '%j%' or id + 2 > id +5";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "orLikeOr");
        $this->assertEquals($expected, $actual);
    }

    public function testBetween()
    {
        $sql = "SELECT * FROM members WHERE age BETWEEN 20 AND 20";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "between");
        $this->assertEquals($expected, $actual);
    }

    public function testNotBetween()
    {
        $sql = "SELECT * FROM members WHERE age NOT BETWEEN 20 AND 20";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "notBetween");
        $this->assertEquals($expected, $actual);
    }

    public function testOrNotBetween()
    {
        $sql = "SELECT * FROM members WHERE age BETWEEN 20 AND 40 OR age NOT BETWEEN 40 AND 45";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "orNotBetween");
        $this->assertEquals($expected, $actual);
    }

    public function testIn()
    {
        $sql = "SELECT * FROM members WHERE age IN(20,30)";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "in");
        $this->assertEquals($expected, $actual);
    }

    public function testNotIn()
    {
        $sql = "SELECT * FROM members WHERE age NOT IN(20,30)";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "notIn");
        $this->assertEquals($expected, $actual);
    }

    public function testNull()
    {
        $sql = "SELECT * FROM members WHERE age IS NULL";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "null");
        $this->assertEquals($expected, $actual);
    }

    public function testNotNull()
    {
        $sql = "SELECT * FROM members WHERE age IS NOT NULL";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "notNull");
        $this->assertEquals($expected, $actual);
    }

    public function testGrouping()
    {
        $sql = "SELECT * FROM members WHERE age > 30 OR (name LIKE 'J%' OR (surname='P' AND name IS NOT NULL)) AND AGE !=30";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "grouping");
        $this->assertEquals($expected, $actual);
    }

    public function testUnGrouped()
    {
        $this->options = array('facade' => 'DB::', 'group' => false);
        $sql = "SELECT * FROM members WHERE age = 25 AND name = 'David'";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "unGrouped");
        $this->assertEquals($expected, $actual);
    }
}