<?php namespace lib\photoimport;

use Excel;

class SheetReader {
  
  protected $mapper;

  public function __construct(ColumnMapper $cm) {
    $this->mapper = $cm;
  }

  public function load($file) {
    return Excel::selectSheetsByIndex(0)->load($file);
  }

  public function getSheet($document) {
    $document->formatDates(true, 'Y-m-d');
    return $document->get();
  }

  public function readRow($row) {
    return $this->mapper->transform($row->all());
  }

  public function read($file) {
    $raw_photos = array();
    $document = $this->load($file);
    $sheet = $this->getSheet($document);
    foreach ($sheet as $row) {
      array_push($raw_photos, $this->readRow($row));
    }
    return $raw_photos;
  }

}