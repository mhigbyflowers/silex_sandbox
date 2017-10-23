<?php

include 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();
$capsule->addConnection([
    "driver"    => "mysql",
    "host"      => "127.0.0.1",
    "database"  => "chatter",
    "username"  => "chatter_api",
    "password"  => "chatter_api",
    "charset"   => "utf8",
    "collation" => "utf8_general_ci",
    "prefix"    => "zxcxz"
]);

$capsule->bootEloquent();
