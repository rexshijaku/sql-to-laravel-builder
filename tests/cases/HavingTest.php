<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class HavingTest extends AbstractCases
{
    private $type = 'having';

    public function testHavingBetween()
    {
        $sql = "SELECT * FROM members HAVING age BETWEEN 25 AND 35";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "between");
        $this->assertEquals($expected, $actual);
    }

    public function testHavingNotBetween()
    {
        $sql = "SELECT * FROM members HAVING age NOT BETWEEN 25 AND 35";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "notBetween");
        $this->assertEquals($expected, $actual);
    }

    public function testHavingCombined()
    {
        $sql = "SELECT name,age,salary,gender FROM members HAVING name like '%R' AND gender = 'm' 
                                               or HAVING salary>1000 AND HAVING gender=0 AND age+2=25";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "combined");
        $this->assertEquals($expected, $actual);
    }

    public function testHaving()
    {
        $sql = "SELECT age,salary FROM members HAVING age > 25, salary < 3000";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "having");
        $this->assertEquals($expected, $actual);
    }

    public function testHavingOperationOnColumn()
    {
        $sql = "SELECT age FROM members HAVING age+20 > 45";;
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "columnOperation");
        $this->assertEquals($expected, $actual);
    }

    public function testHavingOperationOnColumnOr()
    {
        $sql = "SELECT * FROM members HAVING age+20 > 45 OR salary-200 > 500";
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "columnOperationOr");
        $this->assertEquals($expected, $actual);
    }
}