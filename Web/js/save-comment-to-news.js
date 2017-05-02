/**
 * Created by bnzonlia on 27/04/2017.
 */
$('body').on('click','#js-comment-panel legend a.save',function( event ) {
	var $this = $(this);
	event.preventDefault();
	var url = $(this).attr('href');
	var contentNode = $(this).parent().parent().find('textarea').val();
	alert('modification effectué');
	
	// Requête Ajax
	$.ajax( {
		url      : url,
		type     : "POST",
		data :   {
			contenu : contentNode
		},
		dataType : "json",
		success  : function( json, status) {
			console.log(json);
			if (json.code != 0 ) {
				console.error(json.code);
			}
			
			$this.text('Modifier');
			// Supprimer le formulaire
			$this.parents(".js-comment").find("#"+$this.parents(".js-comment").attr('data-id')).remove();
			
			// On rafraîchit les commentaires dans tous les cas : le nouveau commentaire s'affiche s'il est correct.
			refresh_comments();
		},
		
		error : function(resultat, statut, erreur) {
			console.log(resultat.responseText);
		}
	});
});