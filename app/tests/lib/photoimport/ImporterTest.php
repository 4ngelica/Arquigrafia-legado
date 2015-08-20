<?php

use League\FactoryMuffin\Facade as FactoryMuffin;
use Mockery as m;
use lib\photoimport\import\Importer;
use lib\photoimport\import\ImportLogger;
use lib\photoimport\ods\SheetReader;

class ImporterTest extends TestCase {

  protected $photo;
  protected $tag;
  protected $reader;
  protected $logger;
  protected $depencies;

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
    $this->photo = m::mock('Photo');
    $this->tag = m::mock('Tag');
    $this->reader = m::mock('lib\photoimport\ods\SheetReader');
    $this->logger = m::mock('lib\photoimport\import\ImportLogger');
    $this->depencies = array(
        $this->photo,
        $this->tag,
        $this->reader,
        $this->logger
      );
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
    $importer = m::mock( 'lib\photoimport\import\Importer[logError]', $this->depencies );
    $importer->setOds($ods);
    $importer->shouldReceive('logError')->times(3)->with(m::any());

    $importer->logPhotoException($exception, 'photo_tombo');
    $importer->logTagException($exception, 'tag_name');
    $importer->logOdsReadingException($exception);
  }

  public function testShouldImportTags() {
    $importer = App::make('lib\photoimport\import\Importer');
    $tag_data = 'tag1, ,, tag2, tag3, ,,';
    
    $tag1 = new Tag; $tag1->name = 'tag1'; $tag1->save();

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
      array(
        new Photo,
        $this->tag,
        $this->reader,
        $this->logger
      ));
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

}