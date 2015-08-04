<?php namespace lib\photoimport;

use File;

class OdsFileSearcher {

  public function getAllFiles($root) {
    return File::allFiles($root);
  }

  public function search($root) {
    $ods_files = array();
    foreach( $this->getAllFiles($root) as $file ) {
      if ($file->isFile() && $file->getExtension() == 'ods') {
        $file_log = $file->getPathname() . '.log';
        if ( !file_exists($file_log) ) {
          array_push($ods_files, $file);
        }
      }
    }
    return $ods_files;
  }

}