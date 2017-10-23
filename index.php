<?php

require 'vendor/autoload.php';
include 'bootstrap.php';

use Chatter\Models\Message;
use Chatter\Middleware\Logging as ChatterLogging;

$app = new Silex\Application();
$app->before(function ($request, $app) {
    ChatterLogging::log($request, $app);
});

$app->get('/messages', function () use ($app) {
    $_message = new Message();

    $messages = $_message -> all();
    $payload= [];
    foreach ($messages as $_msg) {
        $payload[$_msg->id]=
      [
          'body'=> $_msg->body,
          'user_id'=> $_msg->user_id,
          'created_at'=> $_msg->created_at,
      ];
    }
    return json_encode($payload);
});

$app->run();
