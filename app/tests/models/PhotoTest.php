<?php
/*
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

}
*/