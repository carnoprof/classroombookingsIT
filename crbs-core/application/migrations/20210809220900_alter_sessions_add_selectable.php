<?php defined('BASEPATH') OR exit('Accesso diretto allo script non consentito');

class Migration_Alter_sessions_add_selectable extends CI_Migration
{


	public function up()
	{
		$fields = [
			'is_selectable' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'default' => 0,
				'after' => 'is_current',
			],
		];

		$this->dbforge->add_column('sessions', $fields);
	}


	public function down()
	{
	}


}
