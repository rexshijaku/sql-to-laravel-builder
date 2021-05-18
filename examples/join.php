<?php

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$converter = new SQLToLaravelBuilder();

//==========================================================

$sql = 'SELECT * FROM members JOIN details ON members.id = details.members_id';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->join('details', 'members.id', '=', 'details.members_id')
//              ->get();

//===============================================================

$sql = 'SELECT * FROM members LEFT JOIN details ON members.id = details.members_id';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->leftJoin('details', 'members.id', '=', 'details.members_id')
//              ->get();

//===============================================================

$sql = 'SELECT * FROM members RIGHT JOIN details ON members.id = details.members_id';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->rightJoin('details', 'members.id', '=', 'details.members_id')
//              ->get();


//===============================================================

$sql = 'SELECT * FROM members,details';
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->crossJoin('details')
//              ->get();

//==========================================================