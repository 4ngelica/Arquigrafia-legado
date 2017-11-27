<?php

use League\FactoryMuffin\Facade as FactoryMuffin;
use Mockery as m;

class PhotoTest extends TestCase {

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

	public function testShouldDeletePhotoAfterException() {
		$photo = FactoryMuffin::create('Photo');
		ImageManager::shouldReceive('makeAll')->once()->andThrow('Intervention\Image\Exception\NotWritableException');
		$exception_class = null;
		try {
			$photo->saveImages('image');
		} catch (Exception $e) {
			$exception_class = get_class($e);
		}
		$deleted_photo = Photo::onlyTrashed()->find($photo->id);

		$this->assertEquals('Intervention\Image\Exception\NotWritableException', $exception_class);
		$this->assertNotNull( $deleted_photo );
	}

	public function testShouldUpdateAndRestoreTrashed() {
		$photo = FactoryMuffin::create('Photo');
		$photo->delete(); //soft deleted
		Photo::updateOrCreateWithTrashed( 
			array( 'tombo' => $photo->tombo),
			array( 'tombo' => 'new_tombo')
		);
		/* objeto antigo não está sincronizado com o banco de dados, puxar novamente do banco */
		$photo = Photo::withTrashed()->find($photo->id);

		$this->assertEquals('new_tombo', $photo->tombo);
		$this->assertFalse( $photo->trashed() );
	}

	public function testShouldCretePhotoAndSaveImages() {
		$user = FactoryMuffin::create('User');
		ImageManager::shouldReceive('find')
			->once()->with('basepath/tombo.*')->andReturn('image');
		ImageManager::shouldReceive('getOriginalImageExtension')
			->once()->with('image')->andReturn('jpg');
		$prefix = public_path() . '/arquigrafia-images/1';
		ImageManager::shouldReceive('makeAll')->once()
			->with('image', $prefix, 'jpg');
		$attributes = array(
			'name' => 'name',
			'imageAuthor' => 'author',
			'user_id' => $user->id,
			'tombo' => 'tombo',
		);
		$photo = Photo::import($attributes, 'basepath');

		$this->assertTrue($photo instanceof Photo);
		$this->assertEquals('name', $photo->name);
		$this->assertEquals('author', $photo->imageAuthor);
		$this->assertEquals($user->id, $photo->user_id);
		$this->assertEquals('tombo', $photo->tombo);
		$this->assertEquals('tombo.jpg', $photo->nome_arquivo);
	}

}
