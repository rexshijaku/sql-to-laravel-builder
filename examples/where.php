<?php

use RexShijaku\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$converter = new SQLToLaravelBuilder();

//==========================================================

$sql = "SELECT * FROM members WHERE age = 25";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where('age', '=', 25)
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age = 25 AND name = 'David'";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where([['age', 25], ['name', 'David']])
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age +1  = 25";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->whereRaw('age+1 = ? ', [25])
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age+0 = age";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->whereRaw('age+0 = age')
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age+0  = age or name like '%j%' or id + 2 > id +5";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->whereRaw('age+0 = age')
//              ->orWhere('name', 'LIKE', '%j%')
//              ->orWhereRaw('id+2 > id+5')
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age BETWEEN 20 AND 20";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->whereBetween('age', [20, 20])
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age NOT BETWEEN 20 AND 20";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->whereNotBetween('age', [20, 20])
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age BETWEEN 20 AND 40 OR age NOT BETWEEN 40 AND 45";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->whereBetween('age', [20, 40])
//              ->orWhereNotBetween('age', [40, 45])
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age IN(20,30)";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->whereIn('age', [20, 30])
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age NOT IN(20,30)";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->whereNotIn('age', [20, 30])
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age IS NULL";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->whereNull('age')
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age IS NOT NULL";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->whereNotNull('age')
//              ->get();

//==========================================================

$sql = "SELECT * FROM members WHERE age > 30 OR (name LIKE 'J%' OR (surname='P' AND name IS NOT NULL)) AND AGE !=30";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where('age', '>', 30)
//              ->orWhere(function ($query) {
//                  $query->where('name', 'LIKE', 'J%')
//                      ->orWhere(function ($query) {
//                          $query->where('surname', '=', 'P')
//                              ->whereNotNull('name');
//                      });
//              })
//              ->where('AGE', '!=', 30)
//              ->get();

//==========================================================

$converter = new SQLToLaravelBuilder(array('group' => false));
$sql = "SELECT * FROM members WHERE age = 25 AND name = 'David'";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where('age', '=', 25)
//              ->where('name', '=', 'David')
//              ->get();

//==========================================================