<?php

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

//==========================================================

$converter = new SQLToLaravelBuilder();
$sql = "SELECT * FROM members WHERE age > 25 UNION SELECT * FROM members WHERE name like '%ius%'";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where('age', '>', 25)
//              ->union(DB::table('members')
//                  ->where('name', 'LIKE', '%ius%'))
//              ->get();

//==========================================================
