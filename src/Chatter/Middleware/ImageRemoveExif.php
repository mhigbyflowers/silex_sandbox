<?php

namespace Chatter\Middleware;

class ImageRemoveExif
{
    public static function removeExif($imagepath)
    {
        $extension = substr($imagepath, -4);

        if ('.jpg' == $extension || 'jpeg' == $extension) {
            $pngfile = substr($imagepath, 0, -4) . ".png";
            $_img = imagecreatefromjpeg($imagepath);
            imagepng($_img, $pngfile);

            $imagepath = $pngfile;
        }

        return $imagepath;
    }
}
