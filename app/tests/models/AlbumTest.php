<?php

use League\FactoryMuffin\Facade as FactoryMuffin;
use Mockery as m;

class AlbumTest extends \TestCase {

	public static function setUpBeforeClass()
	{
		FactoryMuffin::loadFactories(app_path() . '/tests/factories');
		FactoryMuffin::setFakerLocale('pt_BR');
	}

	public static function tearDownAfterClass()
	{
		try {
  			FactoryMuffin::deleteSaved();
  			// Album::destroy();
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

	public function testIsInvalidWithouATitle()
	{
		$album = new Album;
		$this->assertFalse($album->save(), 'Album does not pass validation without a title');
	}

	public function testIsValidWithATitle()
	{
		$album = new Album;
		$album->title = 'Teste';
		$this->assertTrue($album->save(), 'Album does pass validation with a title');
	}

	public function testReturnsFalseWithoutCover()
	{
		$album = new Album;
		$this->assertFalse($album->hasCover());
	}

	public function testReturnsTrueWithCover()
	{
		$album = FactoryMuffin::create('Album');
		$this->assertTrue($album->hasCover());
	}

	public function testShouldCreateNewAlbum()
	{
		$user = FactoryMuffin::create('User');
		$cover = FactoryMuffin::create('Photo');
		$attr = [
			'title' => 'test',
			'description' => 'test',
			'user' => $user,
			'cover' => $cover
		];
		$album = Album::create($attr);
		$this->assertInstanceOf('Album', $album);
		$this->assertEquals('test', $album->title);
		$this->assertTrue($user->equal($album->user));
	}

}