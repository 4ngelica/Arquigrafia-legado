<?php

use lib\photoimport\SheetReader;
use lib\photoimport\ColumnMapper;
use Mockery as m;

class SheetReaderTest extends TestCase {

  protected $reader;
  
  protected $mapper_mock;

  public function tearDown()
  {
    m::close();
  }

  public function setUp()
  {
    parent::setUp();
    $this->mapper_mock = $this->mock('lib\photoimport\ColumnMapper');
    $this->reader = App::make('lib\photoimport\SheetReader');
  }

  public function mock($class)
  {
    $mock = m::mock($class);
    $this->app->instance($class, $mock);
    return $mock;
  }

  public function testShouldLoadGivenFileAndReturnDocument() {
    $loader = m::mock('loader');
    $loader->shouldReceive('load')->once()->with('file_path')->andReturn('document');
    Excel::shouldReceive('selectSheetsByIndex')->with(0)->andReturn($loader);
    $return = $this->reader->load('file_path');

    $this->assertEquals('document', $return);
  }

  public function testShouldReturnSheet() {
    $document = m::mock('document');
    $document->shouldReceive('formatDates')->with(true, 'Y-m-d');
    $document->shouldReceive('get')->andReturn('sheet');
    $return = $this->reader->getSheet($document);

    $this->assertEquals('sheet', $return);
  }

  public function testShouldReadAndTransformRowAttributes() {
    $row_mock = m::mock('row');
    $row_mock->shouldReceive('all')->once()->andReturn('raw_attributes');
    $this->mapper_mock->shouldReceive('transform')
      ->once()->with('raw_attributes')->andReturn('new_attributes');
    $return = $this->reader->readRow($row_mock);

    $this->assertEquals('new_attributes', $return);
  }

  public function testShouldReadAllRowsInsideSheet() {
    $sheet = array(1, 2, 3, 4, 5);
    $reader = Mockery::mock('lib\photoimport\SheetReader[load, getSheet, readRow]',
      [$this->mapper_mock]);
    $reader->shouldReceive('load')->with('file')->andReturn('document');
    $reader->shouldReceive('getSheet')->with('document')->andReturn($sheet);
    $reader->shouldReceive('readRow')->times(5)->andReturn(1);
    $return = $reader->read('file');

    $this->assertEquals( array(1,1,1,1,1), $return);
  }

  /**
  * @expectedException PHPExcel_Reader_Exception
  */
  public function testShouldThrowExceptionWithInvalidFile() {
    $return = $this->reader->read('invalid');
  }

}