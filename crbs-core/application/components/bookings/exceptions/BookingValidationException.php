<?php

namespace app\components\bookings\exceptions;


class BookingValidationException extends \RuntimeException
{


	public static function forExistingBooking()
	{
		return new static("Esiste già un altra prenotazione uguale.");
	}


	public static function forHoliday()
	{
		return new static("La prenotazione non può essere creata durante una festività.");
	}


}
