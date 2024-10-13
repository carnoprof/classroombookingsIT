<?php defined('BASEPATH') OR exit('Accesso diretto allo script non consentito');

class Migration_Alter_period_drop_days extends CI_Migration
{


	public function up()
	{
		$this->dbforge->drop_column('periods', 'days');
	}


	public function down()
	{
	}


}
