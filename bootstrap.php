<?php

include 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();
$capsule->addConnection([
    "driver"    => "mysql",
    "host"      => "127.0.0.1",
    "database"  => "chatter",
    "username"  => "root",
    "password"  => "zxc",
    "charset"   => "utf8",
    "collation" => "utf8_general_ci",
    "prefix"    => ""
]);

$capsule->bootEloquent();
