<?php
/**
* @var string $form_id
* @var string $form_action
* @var string $form
* @var string $js_data_action_insert
*/
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<h2>
	Ajouter un commentaire
</h2>
<form id="<?=$form_id?>" action="<?= $form_action ?>" method="post" class="js-form-insert-comment" data-action="<?= isset($js_data_action_insert) ? $js_data_action_insert : '' ?>">
	<p>
		<?= $form ?>
		<input type="submit" value="Commenter" />
	</p>
</form>