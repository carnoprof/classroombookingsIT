<?php

namespace app\components\bookings\exceptions;


class AvailabilityException extends \RuntimeException
{


	public static function forNoWeek()
	{
		return new static("La data selezionata non esiste o non è in una settimana disponibile.");
	}


	public static function forNoPeriods()
	{
		return new static("Non ci sono ore disponibili per la data scelta.");
	}


	public static function forHoliday($holiday = NULL)
	{
		if ( ! is_object($holiday)) {
			return new static('La data scelta rientra in una festività.');
		}

		$format = 'La data scelta rientra in un festività: %s: %s - %s';
		$start = $holiday->date_start->format('d/m/Y');
		$end = $holiday->date_end->format('d/m/Y');
		$str = sprintf($format, $holiday->name, $start, $end);
		return new static($str);
	}


}
