<?php

namespace Chatter\Middleware;

class FileFilter
{
    protected $allowedFiles = ['image/jpeg', 'image/png'];

    public function filter($filesArray, $app)
    {
        $newfile = $filesArray['file'];
        $newfile_type = $newfile['type'];

        if (!in_array($newfile_type, $this->allowedFiles)) {
            $app->abort(415);
        }

        $uploadFileName = $newfile['name'];
        $imagepath = "assets/images/$uploadFileName";
        move_uploaded_file($newfile['tmp_name'], $imagepath);

        return $imagepath;
    }
}
