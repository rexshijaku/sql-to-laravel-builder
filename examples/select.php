<?php

use RexShijaku\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$converter = new SQLToLaravelBuilder();

//==========================================================

$sql = 'SELECT * FROM members';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->get();

//================================================

$sql = 'SELECT * FROM members LIMIT 20, 10';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->offset(20)
//              ->limit(10)
//              ->get();

//=================================================

$sql = 'SELECT name, surname, surname FROM members LIMIT 20, 10';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->select('name', 'surname', 'surname')
//              ->offset(20)
//              ->limit(10)
//              ->get();

//===================================================

$sql = 'SELECT * FROM members WHERE id>100 LIMIT 20, 10';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where('id', '>', 100)
//              ->offset(20)
//              ->limit(10)
//              ->get();

//=====================================================