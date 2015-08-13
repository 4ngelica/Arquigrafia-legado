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
  			Album::destroy();
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

	/**
	* @expectedException Intervention\Image\Exception\NotReadableException
	*/
	public function testShouldNotMakeImage() {
		$photo = FactoryMuffin::create('Photo');
		$photo->makeImage('non_existing_image_path');
	}

	/**
	* @expectedException Exception
	*/
	public function testShouldRaiseExceptionWithouTombo() {
		$photo = new Photo;
		$photo->findImageByTombo('fake_basepath');
	}

	public function testShouldDeletePhotoAfterException() {
		$photo_mock = m::mock('Photo');

		$photo = m::mock('Photo[findImageByTombo, getOriginalImageExtension, updateOrCreate]');

		$photo->shouldReceive('findImageByTombo')
			->with('basepath', 'tombo')
			->andReturn('image');

		$photo->shouldReceive('getOriginalImageExtension')
			->andReturn('jpg');

		$photo->shouldReceive('updateOrCreate')
			->andReturn($photo_mock);

		$photo_mock->shouldReceive('saveImages')
			->andThrow('Exception');

		$photo_mock->shouldReceive('delete')->once();

		$exceptionRaised = false;

		try {
			$photo->updateOrCreateByTombo(
				array('tombo' => 'tombo'),
				'basepath'
			);
		} catch (Exception $e) {
			$exceptionRaised = true;
		}

		$this->assertTrue($exceptionRaised);
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
