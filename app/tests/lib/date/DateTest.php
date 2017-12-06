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

	public function testShouldIdentifyInterval() {
		$raw_date = '2012-11/2013-11';
		$this->assertTrue($this->date->isInterval($raw_date));
	}

	public function testShouldNotIdentifyInterval() {
		$raw_date = '2012-11';
		$this->assertFalse($this->date->isInterval($raw_date));
	}

	public function testShouldIdentifyFullDate() {
		$raw_date = '2012-11-11';
		$this->assertTrue($this->date->isFullDate($raw_date));
	}

	public function testShouldIdentifyPartialDate() {
		$raw_date = '2012-11';
		$raw_date2 = '2012';

		$this->assertTrue($this->date->isPartialDate($raw_date));
		$this->assertTrue($this->date->isPartialDate($raw_date2));
	}

	public function testShouldIdentifyDate() {
		$raw_date = '2012-11-11';
		$raw_date2 = '2012-11';
		$raw_date3 = '2012';

		$this->assertTrue($this->date->isDate($raw_date));
		$this->assertTrue($this->date->isDate($raw_date2));
		$this->assertTrue($this->date->isDate($raw_date3));
	}

	public function testShouldIdentifyFormattedDate() {
		$date1 = '2012-12-12/2015-01-01';
		$date2 = '2010-10/2010-11';
		$date3 = '2010/2011';
		$date4 = '198/199';
		$date5 = '12/13';
		$date6 = '2012-12-12';
		$date7 = '2012-10';
		$date8 = '2012';
		$date9 = '199';
		$date10 = '12';

		$this->assertTrue($this->date->isFormatted($date1));
		$this->assertTrue($this->date->isFormatted($date2));
		$this->assertTrue($this->date->isFormatted($date3));
		$this->assertTrue($this->date->isFormatted($date4));
		$this->assertTrue($this->date->isFormatted($date5));
		$this->assertTrue($this->date->isFormatted($date6));
		$this->assertTrue($this->date->isFormatted($date7));
		$this->assertTrue($this->date->isFormatted($date8));
		$this->assertTrue($this->date->isFormatted($date9));
		$this->assertTrue($this->date->isFormatted($date10));
	}

	public function testShouldNotIdentifyFormattedDate() {
		$date1 = '2012/12/12';
		$date2 = '12/11/2012';

		$this->assertFalse($this->date->isFormatted($date1));
		$this->assertFalse($this->date->isFormatted($date2));
	}

	public function testShouldReturnFormattedDate() {
		$date = '2012-12-12';
		$date2 = '12/12/2012';

		$result = $this->date->formatDate($date);
		$result2 = $this->date->formatDate($date2);

		$this->assertEquals($date, $result);
		$this->assertEquals($date, $result2);
	}

}