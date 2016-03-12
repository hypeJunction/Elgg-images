<?php

$entity = elgg_extract('entity', $vars);

?>
<div>
	<label><?php echo elgg_echo('images:settings:override_file_thumb_dimensions') ?></label>
	<?php
		echo elgg_view('input/select', [
			'name' => 'params[override_file_thumb_dimensions]',
			'value' => $entity->override_file_thumb_dimensions,
			'options_values' => [
				0 => elgg_echo('option:no'),
				1 => elgg_echo('option:yes'),
			]
		]);
	?>
</div>