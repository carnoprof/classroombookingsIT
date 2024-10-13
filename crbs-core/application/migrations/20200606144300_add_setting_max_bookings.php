<?php defined('BASEPATH') OR exit('Accesso diretto allo script non consentito');

class Migration_Add_setting_max_bookings extends CI_Migration
{

	public function up()
	{
		$this->db->insert('settings', [
			'group' => 'crbs',
			'name' => 'num_max_bookings',
			'value' => '0',
		]);
	}


	public function down()
	{
	}


}
