<?php

namespace RexShijaku\SQLToLaravelBuilder\Test;

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

class JoinTest extends AbstractCases
{
    private $type = 'join';

    public function testJoin()
    {
        $sql = 'SELECT * FROM members JOIN details ON members.id = details.members_id';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "join");
        $this->assertEquals($expected, $actual);
    }

    public function testCross()
    {
        $sql = 'SELECT * FROM members,details';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "cross");
        $this->assertEquals($expected, $actual);
    }

    public function testLeft()
    {
        $sql = 'SELECT * FROM members LEFT JOIN details ON members.id = details.members_id';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "left");
        $this->assertEquals($expected, $actual);
    }

    public function testRight()
    {
        $sql = 'SELECT * FROM members RIGHT JOIN details ON members.id = details.members_id';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "right");
        $this->assertEquals($expected, $actual);
    }

    public function testAdvanced()
    {
        $sql = 'SELECT * FROM members JOIN details 
                                  ON members.id = details.members_id 
                                  AND age > 10 AND age NOT BETWEEN 10 AND 20 
                                  AND title IS NOT NULL AND NOT age > 10 AND NAME LIKE "%Jo%" 
                                  AND age NOT IN (10,20,30)
                              LEFT JOIN further_details fd
                                  ON details.id = fd.details_id';
        $converter = new SQLToLaravelBuilder($this->options);
        $actual = $converter->convert($sql);
        $expected = $this->getExpectedValue($this->type, "zAdvanced");
        $this->assertEquals($expected, $actual);
    }


}