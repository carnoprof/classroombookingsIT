<?php

namespace app\components\bookings\exceptions;


class SessionException extends \RuntimeException
{


	public static function notSelected()
	{
		return new static("Nessuna sessione attiva trovata.");
	}


}
