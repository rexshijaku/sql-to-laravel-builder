<?php

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$converter = new SQLToLaravelBuilder();

//==========================================================

$sql = "SELECT age,salary,count(*) FROM members GROUP BY age, salary";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->select('age', 'salary', DB::raw('count(*)'))
//              ->groupBy(['age', 'salary'])->get();

//==========================================================

$converter = new SQLToLaravelBuilder();
$sql = "SELECT age, some_function(),count(*) FROM members GROUP BY age, some_function()";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->select('age', some_function(), DB::raw('some_function()'),DB::raw('count(*)'))
//              ->groupByRaw('age, some_function()')
//              ->get();