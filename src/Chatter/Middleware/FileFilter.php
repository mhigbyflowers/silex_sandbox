<?php
namespace Chatter\Middleware;

class FileFilter
{
  protected $allowedFiles = ['image/jpeg','image/png'];
  public function filter($filesArray,$app)
  {
    $newfile=$filesArray['file'];
    $newfile_type = $newfile[]
  }
}
