<?php

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$converter = new SQLToLaravelBuilder();

//==========================================================

$sql = "SELECT * FROM members ORDER BY name DESC, surname ASC";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->orderBy('name','DESC')
//              ->orderBy('surname','ASC')
//              ->get();

//==========================================================

$sql = "SELECT * FROM members ORDER BY RAND() ASC";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->orderByRaw('RAND() ASC')
//              ->get();

//==========================================================
