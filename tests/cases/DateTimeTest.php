<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class DateTimeTest extends AbstractCases
{
    private $type = 'datetime';

    public function testDate()
    {
        $sql = 'SELECT * FROM members WHERE DATE(created_at) = "2021-03-31" ';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "date");
        $this->assertEquals($expected, $actual);
    }

    public function testDay()
    {
        $sql = 'SELECT * FROM members WHERE DAY(created_at) = 15';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "day");
        $this->assertEquals($expected, $actual);
    }

    public function testMonth()
    {
        $sql = 'SELECT * FROM members WHERE MONTH(created_at) = 12';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "month");
        $this->assertEquals($expected, $actual);
    }

    public function testTime()
    {
        $sql = 'SELECT * FROM members WHERE TIME(created_at) = "11:20:45"';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "time");
        $this->assertEquals($expected, $actual);
    }

    public function testYear()
    {
        $sql = 'SELECT * FROM members WHERE YEAR(created_at) = 1991';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "year");
        $this->assertEquals($expected, $actual);
    }

    // todo remove spaces between '=' and year/date etc
}