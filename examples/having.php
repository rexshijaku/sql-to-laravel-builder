<?php

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$converter = new SQLToLaravelBuilder();

//==========================================================

$sql = "SELECT age,salary FROM members HAVING age > 25, salary < 3000";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->select('age', 'salary')
//              ->having('age', '>', 25)
//              ->having('salary', '<', 3000)
//              ->get();

//==========================================================

$sql = "SELECT age FROM members HAVING age+20 > 25";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->havingRaw('age+20 > ?', [45])
//              ->get();

//==========================================================

$sql = "SELECT * FROM members HAVING age+20 > 45 OR salary-200 > 500";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->havingRaw('age+20 > ?', [45])
//              ->orHavingRaw('salary-200 > ?', [500])
//              ->get();

//==========================================================

$sql = "SELECT * FROM members HAVING age BETWEEN 25 AND 35";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->havingBetween('age', [25, 35])
//              ->get();

//==========================================================

$sql = "SELECT * FROM members HAVING age NOT BETWEEN 25 AND 35";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->havingRaw('age NOT BETWEEN 25 AND 35')
//              ->get();

//==========================================================

$sql = "SELECT name,age,salary,gender FROM members HAVING name like '%R' AND gender = 'm' or HAVING salary>1000 AND HAVING gender=0 AND age+2=25";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->select('name', 'age', 'salary', 'gender')
//              ->having('name', 'like', '%R')
//              ->having('gender', '=', 'm')
//              ->orHaving('salary', '>', 1000)
//              ->having('age', '>=', 25)
//              ->havingRaw('age+2 = ?', [25])
//              ->get();
//==========================================================