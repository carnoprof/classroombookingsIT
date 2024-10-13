<?php

namespace app\components\bookings\exceptions;


class SettingsException extends \RuntimeException
{


	public static function forDisplayType()
	{
		return new static("L'opzione 'Display Type' non è stata selezionata.");
	}


	public static function forColumns()
	{
		return new static("L'opzione 'Display Columns' non è stata selezionata.");
	}


	public static function forNoRooms()
	{
		return new static("Non ci sono stanze disponibili.");
	}


	public static function forNoSchedule()
	{
		return new static("Questo gruppo di stanze non ha degli orari definiti per questa sessione.");
	}


}
