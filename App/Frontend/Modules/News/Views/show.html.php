<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 13/10/2016
 * Time: 10:49
 */


?>


<p>
	Par <em><?= $news['user']['login'] ?></em>, le <?= $news['dateAjout']->format('d/m/Y à H\hi') ?>
</p>
<h2><?= htmlspecialchars( $news[ 'titre' ] ) ?></h2>
<p><?= nl2br( htmlspecialchars( $news[ 'contenu' ] ) ) ?></p>

<?php if ( $news[ 'dateAjout' ] != $news[ 'dateModif' ] ): ?>
	<p style="text-align: right;">
		<small><em>Modifiée le <?= $news[ 'dateModif' ]->format('d/m/Y à H\hi') ?></em></small>
	</p>
<?php endif; ?>

<?php $form_id         = 'insert_comment_1';
$js_data_action_insert = $news -> link_insert_comment_json; ?>
<?php require "insertComment.html.php" ?>

<?php if ( empty( $comments ) ): ?>
	<p id="no-comment">
		Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !
	</p>
<?php endif; ?>
<?php $comments =[] ?>
<?php $js_data_action_refresh = $news ->link_refresh_comments_json; ?>
<div id="js-comment-panel" data-action="<?= $js_data_action_refresh ?>" data-last-update="<?= $dateupdate ?>">
	<?php foreach ( $comments as $comment ): ?>
		<fieldset class="js-comment" data-id=<?= $comment['id']?>>
			<legend>
				Posté par 
				<strong class="js-author">
					<?= htmlspecialchars($comment->User->login()) ?>
				</strong> le <?= $comment[ 'datec' ]->format('d/m/Y à H\hi') ?>
				<?php if ( $comment[ 'datec' ] != $comment[ 'dateu' ] ): ?>
					- <strong class="js-edit-comment">modifié le <?= $comment[ 'dateu' ]->format('d/m/Y à H\hi') ?></strong>
				<?php endif; ?>
				<?php if ($User->isAuthenticated()) { ?> -
					<a id="link1" onclick="Modifier()" href="admin/comment-update-<?= $comment['id'] ?>.html">Modifier</a> |
					<a id="link2" onclick="Delete()" href="admin/comment-delete-<?= $comment['id'] ?>.html">Supprimer</a>
				<?php } ?>
			</legend>
			<p id="contenu" class = "js-comment-content" style="-webkit-hyphens: auto;-moz-hyphens: auto;-ms-hyphens: auto;-o-hyphens: auto;hyphens: auto;width: 390px;">
				<?= nl2br( htmlspecialchars( $comment[ 'contenu' ] ) ) ?>
			</p>
		</fieldset>
	<?php endforeach; ?>
</div>

<?php $form_id         = 'insert_comment_2';
$js_data_action_insert = $news ->link_insert_comment_json; ?>
<?php require "insertComment.html.php" ?>

<script type="text/javascript"  src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="/js/insert-comment-to-news.js"></script>
<script src="/js/refresh-comments-on-news.js"></script>
<script src="/js/update-comment-to-news.js"></script>
<script src="/js/save-comment-to-news.js"></script>
<script src="/js/delete-comment-to-news.js"></script>


