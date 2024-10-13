<?php defined('BASEPATH') OR exit('Accesso diretto allo script non consentito');

class Migration_Create_initial_schedule extends CI_Migration
{


	public function up()
	{
		$this->db->insert('schedules', [
			'schedule_id' => 1,
			'type' => 'periods',
			'name' => 'Periods',
			'description' => null,
		]);
	}


	public function down()
	{
	}

}
