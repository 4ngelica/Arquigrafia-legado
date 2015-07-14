<?php namespace lib\date;

use DateTime;

class Date {

	private static $months = [
		'janeiro',
		'fevereiro',
		'março',
		'abril',
		'maio',
		'junho',
		'julho',
		'agosto',
		'setembro',
		'outubro',
		'novembro',
		'dezembro'
	];

	private static $centuries = [
		'I',
		'II',
		'III',
		'IV',
		'V',
		'VI',
		'VII',
		'VIII',
		'IX',
		'X',
		'XI',
		'XII',
		'XIII',
		'XIV',
		'XV',
		'XVI',
		'XVII',
		'XVIII',
		'XIX',
		'XX',
		'XXI',
		'XXII',
		'XXIII'
	];

	public static function formatDate($date)
	{
		$formattedDate = DateTime::createFromFormat('d/m/Y', $date);
		
		if ($formattedDate &&
			DateTime::getLastErrors()["warning_count"] == 0 &&
			DateTime::getLastErrors()["error_count"] == 0)
				return $formattedDate->format('Y-m-d');

		return null;
	}

	public static function formatDatePortugues($dateTime)
	{
		$formattedDate = DateTime::createFromFormat('Y-m-d', $dateTime);
		
		if ($formattedDate &&
			DateTime::getLastErrors()["warning_count"] == 0 &&
			DateTime::getLastErrors()["error_count"] == 0)
				return $formattedDate->format('d/m/Y');

		return null;

	}

	public static function translate($date) {
		
		if ($date == null) return null;

		//verifica se é um intervalo
		if (strpos($date, '/') !== false) {
			return ucfirst(self::translateInterval($date));
		}
		return ucfirst(self::translateDate($date));	
	}

	public static function translateInterval($date) {
		$dates = explode('/', $date);
		
		if ( self::isCentury($dates[0]) && self::isCentury($dates[1]) )
			return 'entre o ' . self::translateCentury($dates[0]) . ' e o ' . self::translateCentury($dates[1]);

		if ( self::isDecade($dates[0]) && self::isDecade($dates[1]) )
			return 'entre a ' . self::translateDecade($dates[0]) . ' e a ' . self::translateDecade($dates[1]);

		return 'entre ' . self::translateDate($dates[0]) . ' e ' . self::translateDate($dates[1]);
	}

	public static function translateDate($date) {
		if (preg_match('#\d{4,}-\d{2,}-\d{2,}#', $date, $match)) {
			$datetimeObj = DateTime::createFromFormat('Y-m-d', $match[0]);
			$day = $datetimeObj->format('d');
			$monthNumber = intval($datetimeObj->format('m'));
			$year = $datetimeObj->format('Y');
			$month = self::$months[$monthNumber - 1];
			return $day . ' de ' . $month . ' de ' . $year;
		}

		if (preg_match('#\d{4,}-\d{2,}#', $date)) {
			$datetimeObj = DateTime::createFromFormat('Y-m', $date);
			$monthNumber = intval($datetimeObj->format('m'));
			$year = $datetimeObj->format('Y');
			$month = self::$months[$monthNumber - 1];
			return $month . ' de ' . $year;
		}

		if (preg_match('#\d{4,}#', $date)) {
			return $date; //date = year, neste caso
		}

		return null;
	}

	public static function translateCentury($century) {
		return 'século ' . self::$centuries[intval($century)];
	}

	public static function translateDecade($decade) {
		return 'década de ' . (intval($decade) * 10);
	}

	public static function isDecade($date) {
		return strlen($date) == 3 && preg_match('#\d{3,}#', $date);
	}

	public static function isCentury($date) {
		return strlen($date) == 2 && preg_match('#\d{2,}#', $date);
	}

	public static function dateDiff($start,$end=false){
		$stringDate = array();
   
   		try {
      			$start = new DateTime($start);
      			$end = new DateTime($end);
      			$form = $start->diff($end);
   			} catch (Exception $e){
      			return $e->getMessage();
   			}
   
   			$display = array('y'=>'ano',
               'm'=>'mês',
               'd'=>'dia',
               'h'=>'hora',
               'i'=>'minuto');
               //'s'=>'seg');

   			foreach($display as $key => $value){
      			if($form->$key > 0){
         			$stringDate[] = $form->$key.' '.($form->$key > 1 ? $value.'s' : $value);
      			}
   			}
   
   			return implode($stringDate, ', ');
	}
}