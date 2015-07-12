<?php

use League\FactoryMuffin\Facade as FactoryMuffin;

class AlbumTest extends TestCase {

	public static function setupBeforeClass()
	{
		FactoryMuffin::loadFactories(__DIR__ . '/factories');
		FactoryMuffin::setFakerLocale('pt_BR');
	}

	public static function tearDownAfterClass()
	{
  		FactoryMuffin::deleteSaved();
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

	public function testIsInvalidWithouATitle()
	{
		$album = new Album;
		$this->assertFalse($album->validate(), 'Album does not pass validation without a title');
	}

	public function testIsValidWithATitle()
	{
		$album = new Album;
		$album->title = 'Teste';
		$this->assertTrue($album->validate(), 'Album does pass validation with a title');
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

}