<?php

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

//==========================================================

$converter = new SQLToLaravelBuilder();
$sql = "INSERT INTO members (name, surname, age) VALUES ('Jökull', 'Júlíusson', 30)";
echo $converter->convert($sql);
// prints
//          DB::table('members')
//              ->insert(['name' => 'Jökull', 'surname' => 'Júlíusson', 'age' => 30]);

//==========================================================

$converter = new SQLToLaravelBuilder();
$sql = "INSERT INTO members (name,surname, age) VALUES ('Jökull', 'Júlíusson', 30),  ('David', 'Antonsson', null), ('Daniel', 'Kristjansson', null), ('Rubin', 'Pollock', null), ('Þorleifur Gaukur', 'Davíðsson', null) ";
echo $converter->convert($sql);

// prints
//          DB::table('members')
//              ->insert([['name' => 'Jökull', 'surname' => 'Júlíusson', 'age' => 30],
//                  ['name' => 'David', 'surname' => 'Antonsson', 'age' => null],
//                  ['name' => 'Daniel', 'surname' => 'Kristjansson', 'age' => null],
//                  ['name' => 'Rubin', 'surname' => 'Pollock', 'age' => null],
//                  ['name' => 'Þorleifur Gaukur', 'surname' => 'Davíðsson', 'age' => null]]);

//==========================================================