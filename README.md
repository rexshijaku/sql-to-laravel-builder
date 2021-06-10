# Marwan - SQL To Laravel Builder
SQL to Laravel Query Builder, A Converter written in PHP

### Features
- Converts SQL Queries to Laravel Query Builder.
- Assists on building queries as instructed in Laravel Documentation. 
- Provides options to interact with, for generating different results.

### Supports 
Laravel 8

### Demo

##### Online demo
Live demo and free usage is available <a href='https://sql-to-laravel-builder.herokuapp.com/'>here</a>.

### Get Started
##### Install by manual download: 
Download the repository and install required packages by composer.json :

##### Packagist
You can also install it from packagist by running the following command :
```html
composer require rexshijaku/sql-to-laravel-builder
```

### Usage
##### Simple example
#
```php
<?php

use RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder;

require_once dirname(__FILE__) . './vendor/autoload.php';

$options = array('facade' => 'DB::');
$converter = new SQLToLaravelBuilder($options);

$sql = "SELECT COUNT(*) FROM members";
echo $converter->convert($sql);
```
This will produce the following result: 
```php
DB::table('members')->count();
```
##### A more complex example :

```php
$sql = "SELECT
                department_id,
                count(*) 
            FROM
                members
                LEFT JOIN details AS d ON d.member_id = members.member_id 
            WHERE
                ( age = 25 OR ( salary = 2000 AND gender = 'm' ) ) 
                AND id > 15 
            GROUP BY
                department_id 
            HAVING
                height > 1.60";
echo $converter->convert($sql);
```
and this will generate the result below :
```php
DB::table('members')
    ->select('department_id', DB::raw('count(*)'))
    ->leftJoin('details AS d', 'd.member_id', '=', 'members.member_id')
    ->where(function ($query) {
        $query->where('age', '=', 25)
              ->orWhere(function ($query) {
                            $query->where('salary', '=', 2000)
                                  ->where('gender', '=', 'm');
        });
    })
    ->where('id', '>', 15)
    ->groupBy('department_id')
    ->having('height', '>', 1.60)
    ->get();
```
##### Notice 
If you need to change options, or get more comprehensive understanding of provided options then see the following section of Options.
There are dozens of examples for every use case explained in the Query Builder documentation of Laravel 8, which are located in the <a href="https://github.com/rexshijaku/sql-to-laravel-builder/tree/main/examples" target="_blank">examples</a> folder.

### Options
Some important options are briefly explained below:
| Argument  | DataType    | Default  | Description |
| ----- |:----------:| -----:| -----:|
| facade  |  string | DB:: | Facade which allows the access to the Database functionality. |
| group  |  boolean | true | Whether it should group key value pairs into a php array, or generate separate commands for each pair. See an example <a href="https://github.com/rexshijaku/sql-to-laravel-builder/tree/main/examples/where.php" target="_blank">here</a>. |


### How does it works ?
SQL-To-Laravel-Builder is built on top of <a href="hhttps://github.com/greenlion/PHP-SQL-Parser">PHP-SQL-Parser</a>. While <a href="hhttps://github.com/greenlion/PHP-SQL-Parser">PHP-SQL-Parser</a> is responsible for parsing the given SQL Query as input. The result of the  <a href="hhttps://github.com/greenlion/PHP-SQL-Parser">PHP-SQL-Parser</a> is the input of SQL-To-Laravel-Builder.

The structure has three main parts : 
1) Extractors classes - which help to pull out SQL Query parts in a way which are more understandable and processable by Builders. 
2) Builder classes - which help to construct Query Builder methods.
3) Creator - which orchestrates the process between Extractors and Builders in order to produce parts of Query Builder.

### Known issues
- It is not tested in all cases.
- Poor error handling.

### Contributions 
Feel free to contribute on development, testing or eventual bug reporting.

### Support
For general questions about Marwan - SQL-To-Laravel-Builder, tweet at @rexshijaku or write me an email on rexhepshijaku@gmail.com.
To have a quick tutorial check the  <a href="https://github.com/rexshijaku/sql-to-laravel-builder/tree/main/examples" target="_blank">examples</a> folder provided in the repository.

### Author
##### Rexhep Shijaku
 - Email : rexhepshijaku@gmail.com
 - Twitter : https://twitter.com/rexshijaku
 
### Thank you
All contributors who created and are continuously improving <a href="hhttps://github.com/greenlion/PHP-SQL-Parser">PHP-SQL-Parser</a>, without it, this project would be much harder to be realized. 

### In memoriam
For the innocent lives lost (including Marwan al-Masri, aged just six) during the 2021 Israel–Palestine crisis.

### License
MIT License

Copyright (c) 2021 | Rexhep Shijaku

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
