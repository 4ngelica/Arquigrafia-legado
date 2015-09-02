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

  public function testShouldCreateTag() {
    $all_tags = Tag::all();
    $result = Tag::getOrCreate('new_tag');
    $all_new_tags = Tag::all();

    $this->assertTrue( $all_tags->isEmpty() );
    $this->assertTrue( $result instanceof Tag );
    $this->assertEquals( 'new_tag', $result->name );
    $this->assertEquals( 1, $all_new_tags->count() );
  }

  public function testShouldRetrieveExistingTag() {
    $existing_tag = FactoryMuffin::create('Tag');
    $all_tags = Tag::all();
    $result = Tag::getOrCreate($existing_tag->name);
    $all_new_tags = Tag::all();

    $this->assertTrue( $result instanceof Tag );
    $this->assertEquals( $result->name, $existing_tag->name);
    $this->assertEquals( $all_tags->count(), $all_new_tags->count() );
  }

  public function testShouldIncrementTagReferenceCount() {
    $tag = FactoryMuffin::create('Tag');
    $first_count = $tag->count;
    $tag->incrementReferences();
    $second_count = $tag->count;

    $this->assertEquals($first_count + 1, $second_count);

    $tag = new Tag;
    $tag->incrementReferences();

    $this->assertEquals(1, $tag->count);
  }

}