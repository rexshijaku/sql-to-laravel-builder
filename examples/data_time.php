<?php

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

//==========================================================
$converter = new SQLToLaravelBuilder();

$sql = 'SELECT * FROM members WHERE DATE(created_at) = "2021-03-31" ';
echo $converter->convert($sql);
// prints
//            DB::table('members')
//                ->whereDate('created_at', '=', '2021-03-31')
//                ->get();

//==========================================================

$sql = 'SELECT * FROM members WHERE YEAR(created_at) = 1991';
echo $converter->convert($sql);
// prints
//            DB::table('members')
//                ->whereYear('created_at', '=', 1991)
//                ->get();

//==========================================================

$sql = 'SELECT * FROM members WHERE MONTH(created_at) = 12 ';
echo $converter->convert($sql);
//            DB::table('members')
//                ->whereMonth('created_at', '=', 12)
//                ->get();

//==========================================================

$sql = 'SELECT * FROM members WHERE DAY(created_at) = 15 ';
echo $converter->convert($sql);
// prints
//            DB::table('members')
//                ->whereDay('created_at', '=', 15)
//                ->get();

//==========================================================

$sql = 'SELECT * FROM members WHERE TIME(created_at) = "11:20:45" ';
echo $converter->convert($sql);
// prints
//            DB::table('members')
//                ->whereTime('created_at', '=', '11:20:45')
//                ->get();
