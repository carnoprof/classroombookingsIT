<?php defined('BASEPATH') OR exit('Accesso diretto allo script non consentito');

class Migration_Rename_bookings_table extends CI_Migration
{


	public function up()
	{
		$this->dbforge->rename_table('bookings', 'bookings_legacy');
	}


	public function down()
	{
	}


}
