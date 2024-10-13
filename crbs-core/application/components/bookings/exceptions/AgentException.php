<?php

namespace app\components\bookings\exceptions;


class AgentException extends \RuntimeException
{


	public static function forInvalidType($types)
	{
		return new static("Tipo di prenotazione non riconosciuto. Dovrebbe essere uno di questi tipi: " . implode(', ', $types));
	}

	public static function forNoSession()
	{
		return new static('La data riciesta non rientra in nessuna sessione.');
	}


	public static function forNoPeriod()
	{
		return new static('Periodo richiesto non trovato.');
	}


	public static function forNoRoom()
	{
		return new static('La stanza richiesta non esiste o non è disponibile.');
	}


	public static function forInvalidDate()
	{
		return new static('Data richiesta non esiste o non è disponibile.');
	}


	public static function forNoWeek()
	{
		return new static('La data richiesta non esiste o non rientra in nessuna settimana.');
	}


	public static function forNoBooking()
	{
		return new static('La prenotazione non è stata trovata o non esiste.');
	}


}
