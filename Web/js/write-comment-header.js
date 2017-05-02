/**
 * Created by bnzonlia on 20/04/2017.
 */

/**
 * Fabrique le header du commentaire passé en paramètre
 *
 * @param Comment Commentaire à écrire (format JSON)
 */
function write_comment_header( Comment ) {
	console.log( Comment );
	new_comment_header = $( "<legend></legend>" )
		.append( "Posté par ", $( "<strong class=\"js-author\"></strong>" ) );

	new_comment_header.find( ".js-author" ).text(Comment.User.login);

	new_comment_header.append( ' le ', Comment.datec.date );

	if ( Comment.datec.date != Comment.dateu.date ) {
		new_comment_header.append( " - " )
						  .append( $( "<strong class='\"js-edit-comment\"'></strong>" ).text( "modifié le " + Comment.dateu.date ) );
	}

	// Ajout des actions de modification ou suppression au header
	
	
	if (Comment.User.membertype==1)
	{
			new_comment_header.append("\
			<a id='link1' href=\"admin/comment-update-"+Comment.id+".json\">Modifier</a>\
		")
							  .append( " - " )
							  .append("\
			<a id ='link2' href=\"admin/comment-delete-"+Comment.id+".json\">Supprimer</a>\
		");
	}
	else if(Comment.User.membertype==0 )
	{
		new_comment_header.append("\
		<a id='link1' href=\"admin/comment-update-" + Comment.id +".json\">Modifier</a>\
	")
						  .append( " - " )
						  .append("\
		<a id='link2' href=\"admin/comment-delete-" + Comment.id + ".json\">Supprimer</a>\
	");
		if(Comment.User.membertype==3 )
		{
			
		}
	}
	return new_comment_header;
}