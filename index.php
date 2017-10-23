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

//HTTP GET

$app->get('/messages', function () {
    $_message = new Message();

    $messages = $_message->all();

    $payload = [];
    foreach ($messages as $_msg) {
        $payload[$_msg->id] = ['body' => $_msg->body, 'user_id' => $_msg->user_id, 'created_at' => $_msg->created_at, 'updated_at' => $_msg->updated_at, 'file' => $_msg->image_url];
    }

    return json_encode($payload);
});

//HTTP POST

$app->post('/messages', function (Request $request) use ($app) {
    $_message = $request->get('message');

    $newfile = $_Files['files'];
    $uploadFileName = $newfile['name'];
    move_uploaded_file($newfile['tmp_name'], "assets/images/$uploadFileName");
    $imagepath = "assets/images/$uploadFileName";

    $message = new Message();
    $message->body = $_message;
    $message->user_id = -1;
    $message->image_url = $imagepath;
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

//HTTP DELETE
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
