<?php

namespace Chatter\Middleware;

use Aws\S3\S3Client;

class FileMove
{
    public static function move($imagepath, $app)
    {
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => 'us-west-1'
        ]);

        try {
            $s3->putObject([
                'Bucket' => 'silex-sandbox',
                'Key'    => 'AKIAICALBSHGOPSUIOAA',
                'Body'   => fopen($imagepath, 'r'),
                'ACL'    => 'public-read',
            ]);
        } catch (Exception $e) {
            $app->abort(400);
        }

        return $imagepath;
    }
}
