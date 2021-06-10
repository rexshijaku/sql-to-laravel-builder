<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class AggregateTest extends AbstractCases
{
    private $type ='aggregate';

    public function testAvg()
    {
        $sql = 'SELECT AVG(age) FROM members';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "avg");
        $this->assertEquals($expected, $actual);
    }

    public function testAvgWithAlias()
    {
        $sql = 'SELECT AVG(age) as avg_member_age FROM members';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "avgWithAlias");
        $this->assertEquals($expected, $actual);
    }

    public function testMax()
    {
        $sql = 'SELECT MAX(age) FROM members';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "max");
        $this->assertEquals($expected, $actual);
    }

    public function testMaxWithAlias()
    {
        $sql = 'SELECT MAX(age) as age FROM members';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "maxWithAlias");
        $this->assertEquals($expected, $actual);
    }

    public function testMinWithAlias()
    {
        $sql = 'SELECT MIN(age) as age FROM members';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "minWithAlias");
        $this->assertEquals($expected, $actual);
    }

    public function testSum()
    {
        $sql = 'SELECT SUM(age) as sum_age FROM members';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "sum");
        $this->assertEquals($expected, $actual);
    }
}