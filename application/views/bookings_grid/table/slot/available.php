<?php

/* @var $slot app\components\bookings\Slot */

$params = [
	'date' => $slot->date->date,
	'period_id' => $slot->period->period_id,
	'room_id' => $slot->room->room_id,
	'params' => http_build_query($context->get_query_params()),
];

$uri = 'bookings/create/single?' . http_build_query($params);
$url = site_url($uri);

$input_name = sprintf('slots[]');
$input_id = sprintf('slot_chk_%s', $slot->key);
$input_value = json_encode([
	'date' => $slot->date->date,
	'period_id' => $slot->period->period_id,
	'room_id' => $slot->room->room_id,
]);

?>

<td class='<?= $class ?>'>

	<?php if ($this->userauth->is_level(ADMINISTRATOR)): ?>

	<label
		style="display: none"
		class="bookings-grid-button multi-select-content"
		up-show-for=":checked"
		for="<?= $input_id ?>"
	>
		<?php
		echo form_checkbox([
			'name' => $input_name,
			'id' => $input_id,
			'value' => $input_value,
		]);
		?>
	</label>

	<?php endif; ?>

	<button
		class="bookings-grid-button multi-select-content"
		<?php if ($this->userauth->is_level(ADMINISTRATOR)): ?>up-show-for=":unchecked"<?php endif; ?>
		up-href="<?= $url ?>"
		up-modal=".bookings-create"
		up-history="false"
		up-preload
	>&nbsp;
	</button>

</td>
