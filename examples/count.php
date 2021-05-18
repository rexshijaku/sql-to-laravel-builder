<?php

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

//==========================================================
$converter = new SQLToLaravelBuilder();

$sql = "SELECT COUNT(*) FROM members";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->count();

//==========================================================