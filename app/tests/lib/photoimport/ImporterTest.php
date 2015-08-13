<?php

use Mockery as m;
use lib\photoimport\import\Importer;
use lib\photoimport\ods\SheetReader;

class ImporterTest extends TestCase {

  protected $reader;
  protected $photo;
  protected $tag;
  protected $importer;

  public function tearDown()
  {
    m::close();
  }

  public function setUp()
  {
    parent::setUp();
    $this->photo = $this->mock('Photo');
    $this->tag = $this->mock('Tag');
    $this->reader = $this->mock('lib\photoimport\ods\SheetReader');
    $this->importer = App::make('lib\photoimport\import\Importer');
  }

  public function mock($class)
  {
    $mock = m::mock($class);
    $this->app->instance($class, $mock);
    return $mock;
  }

  public function testIsTrue() {
    $this->assertTrue(true);
  }

}