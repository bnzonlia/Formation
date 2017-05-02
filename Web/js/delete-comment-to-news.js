/**
 * Created by bnzonlia on 25/04/2017.
 */
/**
 * Supprime le commentaire en appuyant sur le bouton de suppression
 */
function Delete(event) {
	var Anchors = document.getElementById("link2");
	if( Anchors === undefined || Anchors === null ){
		return;
	}
	Anchors.addEventListener("click", function( event ) {
		var url = $(this).attr('href');
		event.preventDefault();
		
		// Ecriture de la requête Ajax
		$.ajax( {
			url      : url,
			type     : "POST",
			dataType : "json",
			success  : function( json, status ) {
				if (json.code != 0 ) {
					console.error(json.error);
				}
				else {
					refresh_comments();
				}
			},
			
			error : function(resultat, statut, erreur) {
				console.log(resultat);
			}
		});
		
	}, false);
}
document.addEventListener("load", Delete, false);
document.addEventListener("DOMSubtreeModified", Delete, false);