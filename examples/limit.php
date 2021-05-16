<?php

use RexShijaku\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$converter = new SQLToLaravelBuilder();

//==========================================================

$sql = "SELECT * FROM members LIMIT 2";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->limit(1)
//              ->get();

//==========================================================

$sql = "SELECT * FROM members LIMIT 2,1";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->offset(2)
//              ->limit(1)
//              ->get();

//==========================================================