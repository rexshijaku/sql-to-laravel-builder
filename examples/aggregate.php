<?php

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$converter = new SQLToLaravelBuilder();

//==========================================================

$sql = 'SELECT MAX(age) FROM members';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->max('age');

//==========================================================
$sql = 'SELECT MAX(age) as age FROM members';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->selectRaw('MAX(age) AS age')
//              ->get();

//==========================================================

$sql = 'SELECT MIN(age) as age FROM members';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->selectRaw('MIN(age) AS age')
//              ->get();

//==========================================================

$sql = 'SELECT AVG(age) FROM members';
echo $converter->convert($sql);
//          DB::table('members')
//              ->avg('age');

//==========================================================

$sql = 'SELECT AVG(age) as avg_member_age FROM members';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->selectRaw('AVG(age) AS avg_member_age')
//              ->get();

//==========================================================


$sql = 'SELECT SUM(age) as sum_age FROM members';
echo $converter->convert($sql);
// prints
//      DB::table('members')
//          ->selectRaw('SUM(age) AS sum_age')
//          ->get();

//==========================================================