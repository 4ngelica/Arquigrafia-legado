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
		ImageManager::shouldReceive('find')->once()->andReturn('image');
		ImageManager::shouldReceive('getOriginalImageExtension')->once()->andReturn('extension');
		ImageManager::shouldReceive('makeAll')->once()->andThrow('Intervention\Image\Exception\NotWritableException');
		try {
			Photo::updateOrCreateByTombo(
				array(
					'tombo' => $photo->tombo,
					'name' => 'new name'
				),
				'basepath'
			);
		} catch (Exception $e) {
			$exceptionClass = get_class($e);
		}
		$updatedPhoto = Photo::withTrashed()->whereTombo($photo->tombo)->first();
		$this->assertEquals('new name', $updatedPhoto->name);
		$this->assertTrue($updatedPhoto->trashed());
		$this->assertEquals('Intervention\Image\Exception\NotWritableException', $exceptionClass);
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


}
