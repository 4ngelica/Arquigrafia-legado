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

	public function formatDate($date)
	{
		$formattedDate = DateTime::createFromFormat('d/m/Y', $date);
		
		if ($formattedDate &&
			DateTime::getLastErrors()["warning_count"] == 0 &&
			DateTime::getLastErrors()["error_count"] == 0)
				return $formattedDate->format('Y-m-d');

		return null;
	}

	public function formatDatePortugues($dateTime)
	{
		$formattedDate = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
		
		if ($formattedDate &&
			DateTime::getLastErrors()["warning_count"] == 0 &&
			DateTime::getLastErrors()["error_count"] == 0)
				return $formattedDate->format('d/m/Y');

		return null;

	}

	public function translate($date) {
		
		if ($date == null) return null;

		//verifica se é um intervalo
		if (strpos($date, '/') !== false) {
			return ucfirst($this->translateInterval($date));
		}
		if ( $this->isCentury($date) ) {
			return ucfirst($this->translateCentury($date));
		}
		if ( $this->isDecade($date) ) {
			return ucfirst($this->translateDecade($date));	
		}
		return ucfirst($this->translateDate($date));	
	}

	public function translateInterval($date) {
		$dates = explode('/', $date);
		
		if ( $this->isCentury($dates[0]) && $this->isCentury($dates[1]) )
			return 'entre o ' . $this->translateCentury($dates[0]) . ' e o ' . $this->translateCentury($dates[1]);

		if ( $this->isDecade($dates[0]) && $this->isDecade($dates[1]) )
			return 'entre a ' . $this->translateDecade($dates[0]) . ' e a ' . $this->translateDecade($dates[1]);

		return 'entre ' . $this->translateDate($dates[0]) . ' e ' . $this->translateDate($dates[1]);
	}

	public function translateDate($date) {
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

	public function translateCentury($century) {
		return 'século ' . self::$centuries[intval($century)];
	}

	public function translateDecade($decade) {
		return 'década de ' . (intval($decade) * 10);
	}

	public function isDecade($date) {
		return strlen($date) == 3 && preg_match('#\d{3,}#', $date);
	}

	public function isCentury($date) {
		return strlen($date) == 2 && preg_match('#\d{2,}#', $date);
	}

	public function dateDiff($start, $end = false){
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