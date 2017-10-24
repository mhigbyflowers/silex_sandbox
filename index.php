<?php

require_once 'vendor/autoload.php';
include 'bootstrap.php';

use Chatter\Models\Message;
use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Middleware\Authentication as ChatterAuth;
use Chatter\Middleware\FileFilter;
use Chatter\Middleware\FileMove;
use Chatter\Middleware\ImageRemoveExif;
use \Silex\Application;
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
        $payload[$_msg->id] = ['body' => $_msg->body,
                               'user_id' => $_msg->user_id,
                               'user_uri' => '/user/' . $msg->user_id,
                               'created_at' => $_msg->created_at,
                               'image_url' => $_msg->image_url,
                               'messages_url' => $_msg->id,
                               'messages_uri' => '/messages/'. $_msg->id
                             ];
    }

    return json_encode($payload);
});


//setting up Middleware
$filter = function (Request $request, Application $app) {
    $filefilter = new FileFilter();
    $filepath = $filefilter->filter($_FILES, $app);
    $request->headers->set('filepath', $filepath);
};
$removeExif = function (Request $request, Application $app) {
    $filepath = $request->headers->get('filepath');
    $filepath = ImageRemoveExif::removeExif($filepath);
    $request->headers->set('filepath', $filepath);
};
  $move = function (Request $request, Application $app) {
      $filepath = $request->headers->get('filepath');
      $filepath = FileMove::move($filepath, $app);
      $request->headers->set('filepath', $filepath);
  };

//HTTP POST
$app->post('/messages', function (Request $request) use ($app) {
    $_message = $request->get('message');



    $message = new Message();
    $message->body = $_message;
    $message->user_id = -1;
    $message->image_url = $request->headers->get('filepath');
    $message->save();

    if ($message->id) {
        $payload = ['message_id' => $message->id,
                    'message_uri' => '/messages/' . $message->id,
                    'image_url'=>$message->image_url
                  ];
        $code = 201;
    } else {
        $code = 400;
        $payload = [];
    }

    return $app->json($payload, $code);
})->before($filter)->before($removeExif)->before($move);

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
