<?php defined('BASEPATH') OR exit('Accesso diretto allo script non consentito');

class Migration_Remove_school_id extends CI_Migration
{


	public function up()
	{
		$tables = array(
			'academicyears',
			'bookings',
			'departments',
			'holidays',
			'periods',
			'roomfields',
			'rooms',
			'school',
			'users',
			'weekdates',
			'weeks',
		);

		foreach($tables as $table)
		{
			$this->dbforge->drop_column($table, 'school_id');
		}

	}


	public function down()
	{
	}


}
