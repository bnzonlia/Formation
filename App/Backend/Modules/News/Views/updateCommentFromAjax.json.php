<?php
/**
 * Created by PhpStorm.
 * User: bnzonlia
 * Date: 26/04/2017
 * Time: 19:15
 */

/**
 * @var \Entity\Comment $Comment Commentaire édité
 * @var string $form Formulaire à afficher
 */
$data_a = ['Comment' => $Comment];
if (isset($form)) {
	$data_a['form'] = $form;
}
return $data_a;