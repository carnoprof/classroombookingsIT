<?php defined('BASEPATH') OR exit('Accesso diretto allo script non consentito');

class Migration_Alter_rooms_add_pos extends CI_Migration
{

	public function up()
	{
		$fields = [
			'pos' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'default' => 0,
				'null' => FALSE,
			],
		];

		$this->dbforge->add_column('rooms', $fields);
	}



	public function down()
	{
	}


}
