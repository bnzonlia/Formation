
// Délai de rafraîchissement des commentaires
const refresh_delay = 5000;

$(window).on("load", function() {
	timeout = setTimeout(refresh_comments, refresh_delay);
});

$.getScript("/js/write-comment.js");
$.getScript("/js/delete-comment.js");
$.getScript("/js/update-comment.js");
function refresh_comments(){
	// Sélection du bloc de commentaires
	var $this = $( "#js-comment-panel" );
	if ($this.find(".js-comment[data-id]").length) {
		var comments_ids_a = $this.find( ".js-comment[data-id]" ).map( function() {
			return $( this ).data( "id" );
		} ).get().join();
		
	}
	
	// Ecriture de la requête Ajax
	$.ajax( {
		url      : $this.data( 'action' ),
		type     : "POST",
		data     : {
			dateupdate : $this.data( 'last-update' ),
			lastcomment : $this.find( ".js-comment[data-id]" ).first().data('id'),
			comments_ids_a : comments_ids_a,
		},
		dataType : "json",
		success  : function( json, status ,data) {
			if ( json.code != 0 ) {
				console.error( json.error );
				return;
			}
			
			// Ajout des nouveaux commentaires
			var New_comment_a = json.content.New_comment_a;
			
			for ( Comment in New_comment_a ) {
				write_comment(New_comment_a[Comment]);
			}
			
			
			// Modification des  commentaires
			var Update_comment_a = json.content.Update_comment_a;
			
			for ( Comment in Update_comment_a ) {
				update_comment(Update_comment_a[Comment]);
			}
			
			// Suppression des commentaires supprimés
			delete_ids_a = json.content.delete_ids_a;
			for (id in delete_ids_a) {
				delete_comment(delete_ids_a[id]);
			}
			
			if (!$this.find(".js-comment[data-id]").length && delete_ids_a.length != 0) {
				// Remettre le message "pas de commentaire"
				$("<p id=\"no-comment\"></p>").text("Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !").insertAfter($(".js-form-insert-comment")[0]);
			}
			
			// mettre à jour la date de mise à jour dans l'attribut
			$this.data('last-update', json.content.dateupdate);
		},
		
		error : function(resultat, statut, erreur,data) {
			console.log(resultat.responseText);
			
		}
	});
	
}
