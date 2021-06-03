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

//===================Advanced Join Clauses========================

$sql = 'SELECT * FROM members JOIN details 
                                  ON members.id = details.members_id 
                                  AND age > 10 AND age NOT BETWEEN 10 AND 20 
                                  AND title IS NOT NULL AND NOT age > 10 AND NAME LIKE "%Jo%" 
                                  AND age NOT IN (10,20,30)
                              LEFT JOIN further_details fd
                                  ON details.id = fd.details_id';
echo $converter->convert($sql);
// prints
//            DB::table('members')
//                ->join('details', function ($join) {
//                    $join->on('members.id', '=', 'details.members_id')
//                        ->where('age', '>', 10)
//                        ->whereNotBetween('age', [10, 20])
//                        ->whereNotNull('title')
//                        ->whereRaw(' NOT age > ? ', [10])
//                        ->where('NAME', 'LIKE', '%Jo%')
//                        ->whereNotIn('age', [10, 20, 30]);
//                })
//                ->leftJoin(DB::raw('further_details fd'), 'details.id', '=', 'fd.details_id')
//                ->get();