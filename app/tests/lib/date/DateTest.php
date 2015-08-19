<?php

use lib\date\Date as Date;

class DateTest extends TestCase {

	protected $date;

	public function setUp() {
		parent::setUp();
		$this->date = new Date;
	}

	public function testFormatDateReturnsCorrectFormat() {
		$this->assertEquals('2012-11-11', $this->date->formatDate('11/11/2012'));
	}

	public function testFormateDatePortuguesReturnsCorrectFormat() {
		$this->assertEquals('11/11/2012', $this->date->formatDatePortugues('2012-11-11 00:00:00'));	
	}

	public function testTranslateSimpleDates() {
		$raw_date = '2012-11-11';
		$translation = '11 de novembro de 2012';
		$this->assertEquals($translation, $this->date->translate($raw_date));
		
		$raw_date = '2012-11';
		$translation = 'Novembro de 2012';
		$this->assertEquals($translation, $this->date->translate($raw_date));

		$raw_date = '2012';
		$translation = '2012';
		$this->assertEquals($translation, $this->date->translate($raw_date));

		$raw_date = '198';
		$translation = 'Década de 1980';
		$this->assertEquals($translation, $this->date->translate($raw_date));

		$raw_date = '19';
		$translation = 'Século XX';
		$this->assertEquals($translation, $this->date->translate($raw_date));		
	}

	public function testTranslateIntervalDates() {
		$raw_date = '2012-11-11/2013-11-11';
		$translation = 'Entre 11 de novembro de 2012 e 11 de novembro de 2013';
		$this->assertEquals($translation, $this->date->translate($raw_date));
		
		$raw_date = '2012-11/2013-11';
		$translation = 'Entre novembro de 2012 e novembro de 2013';
		$this->assertEquals($translation, $this->date->translate($raw_date));

		$raw_date = '2012/2013';
		$translation = 'Entre 2012 e 2013';
		$this->assertEquals($translation, $this->date->translate($raw_date));

		$raw_date = '197/198';
		$translation = 'Entre a década de 1970 e a década de 1980';
		$this->assertEquals($translation, $this->date->translate($raw_date));

		$raw_date = '18/19';
		$translation = 'Entre o século XIX e o século XX';
		$this->assertEquals($translation, $this->date->translate($raw_date));		

	}

}