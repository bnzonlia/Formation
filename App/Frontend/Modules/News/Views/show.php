<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<p>Par <em><?=$News['user']['login'] ?></em>, le <?= $News['dateAjout']->format('d/m/Y à H\hi') ?></p>
<h2><?= htmlspecialchars($News['titre']) ?></h2>
<p><?= nl2br($News['contenu']) ?></p>

<?php if ($News['dateAjout'] != $News['dateModif']) { ?>
	<p style="text-align: right;"><small><em>Modifiée le <?= $News['dateModif']->format('d/m/Y à H\hi') ?></em></small></p>
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
			<strong><?= htmlspecialchars($comment['auteur']) ?></strong> le <?= $comment['date']->format('d/m/Y à H\hi') ?>
			<?php if ($user->isAuthenticated()) { ?> -
				<a href="admin/comment-update-<?= $comment['id'] ?>.html">Modifier</a> |
				<a href="admin/comment-delete-<?= $comment['id'] ?>.html">Supprimer</a>
			<?php } ?>
		</legend>
		<p><?= nl2br(htmlspecialchars($comment['contenu'])) ?></p>
	</fieldset>
	<?php
}
?>

<p><a href="commenter-<?= $news['id'] ?>.html">Ajouter un commentaire</a></p>