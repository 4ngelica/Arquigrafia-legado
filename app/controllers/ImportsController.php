<?php

use lib\photoimport\SheetReader;
use lib\photoimport\OdsFileSearcher;
use lib\photoimport\Importer;

class ImportsController extends \BaseController {

  protected $ods_searcher;

  public function __construct(OdsFileSearcher $ofs) {
    $this->ods_searcher = $ofs;
  }

  public function import() {
    //call importer
  }

}