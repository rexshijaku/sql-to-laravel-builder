<?php

use RexShijaku\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

//==========================================================

$converter = new SQLToLaravelBuilder();
$sql = "SELECT * FROM members WHERE name LIKE '%j%' AND  surname LIKE 'j%' or title not LIKE '%gui'";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->where('name', 'LIKE', '%j%')
//              ->where('surname', 'LIKE', 'j%')
//              ->orWhere('title', 'NOT LIKE', '%gui')
//              ->get();

//==========================================================