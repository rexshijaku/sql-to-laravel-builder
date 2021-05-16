<?php

use RexShijaku\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

//==========================================================

$converter = new SQLToLaravelBuilder();
$sql = " SELECT * FROM (members) WHERE ( age = 25 OR ( salary = 2000 AND gender = 'm' ) ) AND id > 100800";
echo $converter->convert($sql);
// prints
          DB::table(DB::raw('members'))
              ->where(function ($query) {
                  $query->where('age', '=', 25)
                      ->orWhere(function ($query) {
                          $query->where('salary', '=', 2000)
                              ->where('gender', '=', 'm');
                      });
              })
              ->where('id', '>', 100800)
              ->get();

//==========================================================