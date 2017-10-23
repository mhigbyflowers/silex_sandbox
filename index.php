<?php

require_once 'vendor/autoload.php';
include 'bootstrap.php';

use Chatter\Models\Message;
use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Middleware\Authentication as ChatterAuth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app->before(function ($request, $app) {
    ChatterLogging::log($request, $app);
    ChatterAuth::authenticate($request, $app);
});

$app->get('/messages', function () {
    $_message = new Message();

    $messages = $_message->all();

    $payload = [];
    foreach ($messages as $_msg) {
        $payload[$_msg->id] = ['body' => $_msg->body, 'user_id' => $_msg->user_id, 'created_at' => $_msg->created_at, 'updated_at' => $_msg->updated_at];
    }

    return json_encode($payload);
});

$app->post('/messages', function (Request $request) use ($app) {
    $_message = $request->get('message');

    $message = new Message();
    $message->body = $_message;
    $message->user_id = -1;
    $message->save();

    if ($message->id) {
        $payload = ['message_id' => $message->id, 'message_uri' => '/messages/' . $message->id];
        $code = 201;
    } else {
        $code = 400;
        $payload = [];
    }

    return $app->json($payload, $code);
});

$app->delete('/messages/{message_id}', function ($message_id) use ($app) {
    $message = Message::find($message_id);
    $message->delete();

    if ($message->exists) {
        return new Response('', 400);
    } else {
        return new Response('', 204);
    }
});

$app->run();
