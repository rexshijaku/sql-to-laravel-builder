<?php

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

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

$sql = "DELETE FROM members WHERE NOT (age>10 AND salary > 2000 OR age>9) OR NOT (age < 10)";

echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where(function ($query) {
//                  $query->where('age', '>', 10)->where('salary', '>', 2000)->orWhere('age', '>', 9);
//              }, null, null, 'and not')
//              ->where(function ($query) {
//                  $query->where('age', '<', 10);
//              }, null, null, 'or not')
//              ->delete();

//==========================================================