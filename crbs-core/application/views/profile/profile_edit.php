<?php

echo $this->session->flashdata('saved');

echo form_open('profile/save', array('class' => 'cssform', 'id' => 'profile_edit'));

?>


<fieldset>

	<legend accesskey="U" tabindex="<?php tab_index() ?>">Informazioni utente</legend>

	<p>
	  <label for="email" class="required">Indirizzo e-mail</label>
	  <?php
		$email = set_value('email', $user->email, FALSE);
		echo form_input(array(
			'name' => 'email',
			'id' => 'email',
			'size' => '35',
			'maxlength' => '255',
			'tabindex' =>tab_index(),
			'value' => $email,
		));
		?>
	</p>
	<?php echo form_error('email'); ?>


	<p>
	  <label for="password1">Password</label>
	  <?php
		echo form_password(array(
			'name' => 'password1',
			'id' => 'password1',
			'size' => '20',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
	</p>
	<?php echo form_error('password1'); ?>


	<p>
	  <label for="password2">Password (ripeti)</label>
	  <?php
		echo form_password(array(
			'name' => 'password2',
			'id' => 'password2',
			'size' => '20',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
	</p>
	<?php echo form_error('password2'); ?>


</fieldset>


<fieldset>


	<p>
	  <label for="firstname">Nome</label>
	  <?php
		$firstname = set_value('firstname', $user->firstname, FALSE);
		echo form_input(array(
			'name' => 'firstname',
			'id' => 'firstname',
			'size' => '20',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $firstname,
		));
		?>
	</p>
	<?php echo form_error('firstname'); ?>


	<p>
	  <label for="lastname">Cognome</label>
	  <?php
		$lastname = set_value('lastname', $user->lastname, FALSE);
		echo form_input(array(
			'name' => 'lastname',
			'id' => 'lastname',
			'size' => '20',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $lastname,
		));
		?>
	</p>
	<?php echo form_error('lastname'); ?>


	<p>
	  <label for="displayname">Nome visualizzato</label>
	  <?php
		$displayname = set_value('displayname', $user->displayname, FALSE);
		echo form_input(array(
			'name' => 'displayname',
			'id' => 'displayname',
			'size' => '20',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $displayname,
		));
		?>
	</p>
	<?php echo form_error('displayname'); ?>

<!--
	<p>
	  <label for="ext">Extension</label>
	  <?php
		$ext = set_value('ext', $user->ext, FALSE);
		echo form_input(array(
			'name' => 'ext',
			'id' => 'ext',
			'size' => '10',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $ext,
		));
		?>
	</p>
	<?php echo form_error('ext'); ?>
-->

</fieldset>


<?php
$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
));

echo form_close();
