<?php

namespace app\components\bookings\exceptions;


class DateException extends \RuntimeException
{


	public static function invalidDate($date_string)
	{
		return new static(sprintf("Data non seleionata o data non valida (%s).", $date_string));
	}


	public static function forSessionRange($datetime)
	{
		if ($datetime) {
			$dt = $datetime->format('d/m/Y');
		} else {
			$dt = 'None';
		}

		return new static(sprintf("La data selezionata (%s), non è all'interno della sessione corrente.", $dt));
	}


}
