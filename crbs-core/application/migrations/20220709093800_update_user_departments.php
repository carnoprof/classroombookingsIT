<?php defined('BASEPATH') OR exit('Accesso diretto allo script non consentito');

class Migration_update_user_departments extends CI_Migration
{

	public function up()
	{
		$sql = "UPDATE users SET department_id = NULL WHERE department_id = 0";
		$this->db->query($sql);
	}


	public function down()
	{
	}


}
