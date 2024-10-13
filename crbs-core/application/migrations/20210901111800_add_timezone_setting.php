<?php defined('BASEPATH') OR exit('Accesso diretto allo script non consentito');

class Migration_Add_timezone_setting extends CI_Migration
{

	public function up()
	{
		$tz = date_default_timezone_get();
		if ($tz === 'UTC') $tz = 'Europe/London';

		$this->db->insert('settings', [
			'group' => 'crbs',
			'name' => 'timezone',
			'value' => $tz,
		]);
	}


	public function down()
	{
	}


}
