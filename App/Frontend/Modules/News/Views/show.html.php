<p>Par <em><?= $news['user']['login'] ?></em>, le <?= $news['dateAjout']->format('d/m/Y à H\hi') ?></p>
<h2><?= $news['titre'] ?></h2>
<p><?= nl2br($news['contenu']) ?></p>

<?php if ($news['dateAjout'] != $news['dateModif']) { ?>
	<p style="text-align: right;"><small><em>Modifiée le <?= $news['dateModif']->format('d/m/Y à H\hi') ?></em></small></p>
<?php } ?>

<p><a href="commenter-<?= $news['id'] ?>.html">Ajouter un commentaire</a></p>

<?php
if (empty($comments))
{
	?>
	<p>Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
	<?php
}
foreach ($comments as $comment)
{
	?>

	<fieldset>
		<legend>
			Posté par <strong><?= htmlspecialchars($comment->User->login()) ?></strong> le <?= $comment['datec']->format('d/m/Y à H\hi') ?>
			<?php if ($comment['datec'] != $comment['dateu']) { ?>
				<p style="text-align: right;"><small><em>Modifiée le <?= $comment['dateu']->format('d/m/Y à H\hi') ?></em></small></p>
			<?php } ?>
			<?php if ($User->isAuthenticated()) { ?> -
				<a href="admin/comment-update-<?= $comment['id'] ?>.html">Modifier</a> |
				<a href="admin/comment-delete-<?= $comment['id'] ?>.html">Supprimer</a>
			<?php } ?>
		</legend>
		<div style="-webkit-hyphens: auto;-moz-hyphens: auto;-ms-hyphens: auto;-o-hyphens: auto;hyphens: auto;width: 390px;">
		<p><?= nl2br(htmlspecialchars($comment['contenu'])) ?></p>
		</div>
	</fieldset>

	<?php
}
?>

<p><a href="commenter-<?= $news['id'] ?>.html">Ajouter un commentaire</a></p>