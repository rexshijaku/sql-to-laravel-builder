<?php

use RexShijaku\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$converter = new SQLToLaravelBuilder();

//==========================================================

$sql = "UPDATE members SET age = 10 WHERE id = 2";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where('id', '=', 2)
//              ->update(['age' => 34]);

//==========================================================

$sql = "UPDATE members SET age = age+10 WHERE id = 2";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where('id', '=', 2)
//              ->update(['age' => DB::raw('age+10')]);

//==========================================================