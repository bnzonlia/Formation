/**
 * Created by bnzonlia on 20/04/2017.
 */
$.getScript("/js/write-comment-header.js");

/**
 * Met à jour un commentaire sur l'affichage
 * @param Comment Données JSON d'un commentaire
 */
function update_comment( Comment ) {
	var html_comment     = $( ".js-comment[data-id=" + Comment.id + "]" );
	var date_update_text = $( html_comment ).find( ".js-edit-comment " );
	if ( Comment.datec.date != Comment.dateu.date ) {
		if ( !date_update_text.length ) {
			// Recréer le header complet : on supprime d'abord
			html_comment.find( "legend" ).remove();
			
			// Insérer le header
			html_comment.prepend( write_comment_header(Comment) );
		}
		else {
			date_update_text.text( " modifié le " + Comment.dateu.date );
		}
	}
	
	var content_node = html_comment.find( ".js-comment-content" );
	if (content_node.length) {
		content_node.text( Comment.contenu.replace( '\n', '<br />' ) );
	}
	else {
		html_comment.append( $( "<p class=\"js-comment-content\"></p>" ).html( Comment.contenu.replace( '\n', '<br />' ) ) );
	}
	
}