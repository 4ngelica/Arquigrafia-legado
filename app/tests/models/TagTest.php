<?php

use League\FactoryMuffin\Facade as FactoryMuffin;
use Mockery as m;

class TagTest extends TestCase {
  
  public static function setUpBeforeClass()
  {
    FactoryMuffin::loadFactories(app_path() . '/tests/factories');
    FactoryMuffin::setFakerLocale('pt_BR');
  }

  public static function tearDownAfterClass()
  {
    try {
        FactoryMuffin::deleteSaved();
    } catch (Exception $e) {
      ;
    }
  }

  private function prepareForTests()
  {
    Artisan::call('migrate');
  }

  public function setUp()
  {
    parent::setUp();
    $this->prepareForTests();
  }

  public function tearDown()
  {
    m::close();
  }

  public function testShouldReturnArrayOfTags() {
    $raw_tags = 'tag1, tag2, tag3';
    $return = Tag::transform($raw_tags);

    $this->assertTrue(is_array($return));
    $this->assertEquals(3, count($return));
    $this->assertEquals(array('tag1', 'tag2', 'tag3'), $return);

  }

  public function testShouldReturnEmptyArray() {
    $raw_tags = '    , ,,,    , , ,   ,  ,  ,  ,  ,   ,  ';
    $return = Tag::transform($raw_tags);
    $this->assertTrue(empty($return));
  }

}