<?php

use League\FactoryMuffin\Facade as FactoryMuffin;
use Mockery as m;
use lib\photoimport\import\Importer;
use lib\photoimport\import\ImportLogger;
use lib\photoimport\ods\SheetReader;
use lib\photoimport\ods\OdsFileSearcher;

class ImporterTest extends TestCase {

  protected $reader;
  protected $logger;
  protected $ods_searcher;
  protected $dependencies;

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

  public function tearDown()
  {
    m::close();
  }

  private function prepareForTests()
  {
    Artisan::call('migrate');
    $this->reader = m::mock('lib\photoimport\ods\SheetReader');
    $this->logger = m::mock('lib\photoimport\import\ImportLogger');
    $this->ods_searcher = m::mock('lib\photoimport\ods\OdsFileSearcher');
    $this->dependencies = array(
        $this->reader,
        $this->logger,
        $this->ods_searcher
      );
    $exiv2 = m::mock('lib\metadata\Exiv2');
    Exiv2::shouldReceive('getInstance')->andReturn($exiv2);
    $exiv2->shouldReceive('saveMetadata');

  }

  public function setUp()
  {
    parent::setUp();
    $this->prepareForTests();
  }

  public function mock($class)
  {
    $mock = m::mock($class);
    $this->app->instance($class, $mock);
    return $mock;
  }

  public function testShouldLogError() {
    $import_logger = $this->mock('lib\photoimport\import\ImportLogger');
    $import_logger->shouldReceive('addError')->once();

    $ods = m::mock('lib\photoimport\ods\OdsFile');
    $ods->shouldReceive('logError')->once();

    $importer = App::make('lib\photoimport\import\Importer');
    $importer->setOds($ods);

    $importer->logError('message');
  }
  public function testExceptionsAreLogged() {
    $exception = m::mock('fake_exception');
    $exception->shouldReceive('getMessage')
      ->times(3)->andReturn('exception_message');
    

    $ods = m::mock('lib\photoimport\ods\OdsFile');
    $ods->shouldReceive('getPathname')->once()->andReturn('ods_pathname');
    $importer = m::mock( 'lib\photoimport\import\Importer[logError]', $this->dependencies );
    $importer->setOds($ods);
    $importer->shouldReceive('logError')->times(3)->with(m::any());

    $importer->logPhotoException($exception, 'photo_tombo');
    $importer->logTagException($exception, 'tag_name');
    $importer->logOdsReadingException($exception);
  }

  public function testShouldImportTags() {
    $importer = m::mock('lib\photoimport\import\Importer[logFoundTags, logImportedTagsCount]',
      $this->dependencies );
    $tag_data = 'tag1, ,, tag2, tag3, ,,';
    $tag1 = new Tag; $tag1->name = 'tag1'; $tag1->save();
    $importer->shouldReceive('logFoundTags')->once()
      ->with(m::type('array'));
    $importer->shouldReceive('logImportedTagsCount')->once()
      ->with(m::type('array'));
    $result = $importer->importTags($tag_data);

    $this->assertTrue( is_array($result) );
    $this->assertEquals( 3, count($result) );
    $this->assertTrue($result[0] instanceof Tag);
    $this->assertTrue($result[1] instanceof Tag);
    $this->assertTrue($result[2] instanceof Tag);
    $this->assertEquals('tag1', $result[0]->name);
    $this->assertEquals('tag2', $result[1]->name);
    $this->assertEquals('tag3', $result[2]->name);
    $this->assertEquals($tag1->id, $result[0]->id);
  }

  protected function setUpImportPhoto($user, $attributes) {
    ImageManager::shouldReceive('find')->once()
      ->with('basepath/tombo.*')->andReturn('image');
    ImageManager::shouldReceive('getOriginalImageExtension')->once()
      ->with('image')->andReturn('extension');
    ImageManager::shouldReceive('makeAll')->once()
      ->with('image', m::any(), 'extension');
    $importer = m::mock('lib\photoimport\import\Importer[logImportedPhoto]',
      $this->dependencies);
    $importer->shouldReceive('logImportedPhoto')->once()
      ->with( m::type('Photo') );

    $result = $importer->importPhoto($attributes, 'basepath');
    
    $this->assertTrue($result instanceof Photo);
    $this->assertEquals('new photo', $result->name);
    $this->assertEquals('new author', $result->imageAuthor);
    $this->assertEquals($user->id, $result->user_id);
    $this->assertEquals('tombo.extension', $result->nome_arquivo);
    return $result;
  }

  public function testShouldImportNewPhotoAndLogIt() {
    $user = FactoryMuffin::create('User');
    $attributes = array(
        'name' => 'new photo',
        'imageAuthor' => 'new author',
        'tombo' => 'tombo',
        'user_id' => $user->id
      );
    $this->setUpImportPhoto($user, $attributes);
  }

  public function testShouldImportDeletedPhotoAndLogIt() {
    $user = FactoryMuffin::create('User');
    $attributes = array(
        'name' => 'new photo',
        'imageAuthor' => 'new author',
        'tombo' => 'tombo',
        'user_id' => $user->id
      );
    $p = Photo::create( array_add($attributes, 'nome_arquivo', 'tombo.ext') );
    $p->delete();

    $result = $this->setUpImportPhoto($user, $attributes);
    $this->assertFalse($result->trashed());
  }

  public function testShouldImportPhotoAndTags() {
    $photo = m::mock('Photo');
    $tags = array( m::mock('Tag'), m::mock('Tag') );
    $photo->shouldReceive('syncTags')->once()->with($tags);
    $importer = m::mock('lib\photoimport\import\Importer' .
      '[importPhoto, importTags]', $this->dependencies);
    $importer->shouldReceive('importPhoto')
      ->once()->with('photo_data', 'basepath')->andReturn($photo);
    $importer->shouldReceive('importTags')
      ->once()->with('tag_data')->andReturn($tags);

    $result = $importer->import('basepath', 'photo_data', 'tag_data');

    $this->assertTrue($result instanceof Photo);
  }

  public function testSouldImportAllPhotosFromContent() {
    
    $tag1 = FactoryMuffin::instance('Tag')->name;
    $tag2 = FactoryMuffin::instance('Tag')->name;
    $tag3 = FactoryMuffin::instance('Tag')->name;
    $tag4 = FactoryMuffin::instance('Tag')->name;
    $photo_data1 = FactoryMuffin::instance('Photo')->attributesToArray();
    $photo_data2 = FactoryMuffin::instance('Photo')->attributesToArray();
    unset($photo_data1['nome_arquivo']);
    unset($photo_data1['user_id']);
    unset($photo_data2['nome_arquivo']);
    unset($photo_data2['user_id']);

    $user = FactoryMuffin::create('User');
    $photo_data1['tags'] = $tag1 . ',' . $tag2;
    $photo_data2['tags'] = $tag3 . ',' . $tag4;
    $content = array($photo_data1, $photo_data2);
    ImageManager::shouldReceive('find')->times(2)
      ->with(m::type('string'))->andReturn('image');
    ImageManager::shouldReceive('getOriginalImageExtension')->times(2)
      ->with('image')->andReturn('extension');
    ImageManager::shouldReceive('makeAll')->times(2)
      ->with('image', m::type('string'), 'extension');
    $importer = m::mock('lib\photoimport\import\Importer' .
        '[logError, logInfo]', $this->dependencies);
    $importer->shouldReceive('logInfo')->times(6)
      ->with(m::type('string'));
    $importer->shouldReceive('logError')->times(0);
    $ods = m::mock('lib\photoimport\ods\OdsFile');
    $ods->shouldReceive('getBasePath')->once()->andReturn('basepath');
    $importer->setUser($user);
    $importer->setOds($ods);

    $result = $importer->importContent($content);

    $this->assertEquals(2, count($result));
    $this->assertTrue($result[0] instanceof Photo);
    $this->assertTrue($result[1] instanceof Photo);
    $tags1 = $result[0]->tags->keyBy('name');
    $tags2 = $result[1]->tags->keyBy('name');
    $this->assertEquals(2, $tags1->count());
    $this->assertEquals(2, $tags2->count());
    $this->assertTrue($tags1->has($tag1));
    $this->assertTrue($tags1->has($tag2));
    $this->assertTrue($tags2->has($tag3));
    $this->assertTrue($tags2->has($tag4));
  }

  public function testSouldImportJustOnePhotoFromContent() {
    $tag1 = FactoryMuffin::instance('Tag')->name;
    $tag2 = FactoryMuffin::instance('Tag')->name;
    $tag3 = FactoryMuffin::instance('Tag')->name;
    $tag4 = FactoryMuffin::instance('Tag')->name;
    $photo_data1 = FactoryMuffin::instance('Photo')->attributesToArray();
    $photo_data2 = FactoryMuffin::instance('Photo')->attributesToArray();
    unset($photo_data1['nome_arquivo']);
    unset($photo_data1['user_id']);
    unset($photo_data2['nome_arquivo']);
    unset($photo_data2['user_id']);

    $user = FactoryMuffin::create('User');
    $photo_data1['tags'] = $tag1 . ',' . $tag2;
    $photo_data2['tags'] = $tag3 . ',' . $tag4;
    $content = array($photo_data1, $photo_data2);
    
    ImageManager::shouldReceive('find')->once()
      ->with('basepath/' . $photo_data1['tombo'] . '.*')->andReturn('image');
    
    ImageManager::shouldReceive('find')->once()
      ->with('basepath/' . $photo_data2['tombo'] . '.*')->andReturn('image2');
    
    ImageManager::shouldReceive('getOriginalImageExtension')->times(2)
      ->with(m::type('string'))->andReturn('extension');
    
    ImageManager::shouldReceive('makeAll')->once()
      ->with('image', m::type('string'), 'extension');
    
    ImageManager::shouldReceive('makeAll')->once()
      ->with('image2', m::type('string'), 'extension')
      ->andThrow('Intervention\Image\Exception\NotWritableException');
    
    $importer = m::mock('lib\photoimport\import\Importer' .
        '[logError, logInfo]', $this->dependencies);
    $importer->shouldReceive('logInfo')->times(3)
      ->with(m::type('string'));
    $importer->shouldReceive('logError')->once();
    $ods = m::mock('lib\photoimport\ods\OdsFile');
    $ods->shouldReceive('getBasePath')->once()->andReturn('basepath');
    $importer->setUser($user);
    $importer->setOds($ods);

    $result = $importer->importContent($content);

    $this->assertEquals(1, count($result));
    $this->assertTrue($result[0] instanceof Photo);
    $tags1 = $result[0]->tags->keyBy('name');
    $this->assertEquals(2, $tags1->count());
    $this->assertTrue($tags1->has($tag1));
    $this->assertTrue($tags1->has($tag2));
  }

}