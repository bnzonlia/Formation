/**
 * Created by bnzonlia on 20/04/2017.
 */
$.getScript("/js/write-comment-header.js");

/**
 * Ecrit le commentaire à partir d'un commentaire donné sous format JSON.
 * Le commentaire doit être validé préalablement.
 */
function write_comment( Comment ) {

	// Structure de base avec le header du message
	var new_comment = $( "<fieldset class=\"js-comment\" data-id=\""+Comment.id+"\"></fieldset>" )
		.append( write_comment_header(Comment))
		

		// Ajout du contenu du nouveau commentaire
		new_comment.append( $( " <p class=\"js-comment-content \" style=\"-webkit-hyphens: auto;-moz-hyphens: auto;-ms-hyphens: auto;-o-hyphens: auto;hyphens: auto;width: 390px;\" ></p>" ).html( Comment.contenu.replace( '\n', '<br />' ) ) );
	// Supprimer le message "pas de commentaires" si besoin
	var no_comment_message = $( "#no-comment" );
	if ( no_comment_message ) {
		// S'il n'y a pas encore de commentaire, on retire le message disant qu'il n'y a pas de commentaire.
		no_comment_message.remove();
	}
	$("#js-comment-panel").prepend (new_comment);
}
