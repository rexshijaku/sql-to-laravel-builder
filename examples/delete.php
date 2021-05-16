<?php

use RexShijaku\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$converter = new SQLToLaravelBuilder();

//==========================================================

$sql = "DELETE FROM members";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->delete();

//==========================================================

$sql = "DELETE FROM members WHERE age>10";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where('age','>',10)
//              ->delete();
//==========================================================

$sql = "DELETE FROM members WHERE age>10 OR salary > 2000";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where('age','>',10)
//              ->orWhere('salary','>',2000)
//              ->delete();

//==========================================================

$sql = "DELETE FROM members WHERE not (age>10 AND salary > 2000)";

echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where([['age','>',10],['salary','>',2000]])
//              ->delete();

//==========================================================