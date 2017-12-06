<?php

use League\FactoryMuffin\Facade as FactoryMuffin;
use Mockery as m;

class BaseModelTest extends TestCase {

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

	public function testShouldBeEquals()
	{
		$b1 = new BaseModel;
		$b1->id = 1;
		$b2 = new BaseModel;
		$b2->id = 1;
		$this->assertTrue($b1->equal($b2));
	}

	public function testShouldNotBeEquals()
	{
		$b1 = new BaseModel;
		$b1->id = 1;
		$b2 = new BaseModel;
		$b2->id = 2;
		$p = FactoryMuffin::create('User');
		$this->assertFalse($b1->equal($b2));
		$this->assertFalse($b1->equal('test'));
		$this->assertFalse($b1->equal($p));
	}

}