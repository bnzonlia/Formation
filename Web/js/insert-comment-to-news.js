/**
 * Created by bnzonlia on 20/04/2017.
 */
$.getScript("/js/write-comment.js");

// Fonction pour traiter l'envoi du formulaire
$( '.js-form-insert-comment' ).submit( function( event ) {
	var $this = $( this );

	// Empêcher l'envoi au contrôleur html
	event.preventDefault();

	// Ecriture de la requête Ajax
	$.ajax( {
		url      : $this.data( 'action' ),
		type     : "POST",
		data     : {
			auteur  : $this.find( '[name=auteur]' ).val(),
			contenu : $this.find( '[name=contenu]' ).val(),
		},
		dataType : "json",
		success  : function( json, status ) {
			// Cette fonction se déclenche dès lors que l'URL est trouvée !
			if ( json.code != 0 ) {
				console.error( ' j\'ai rencontré une erreur' );
				return;
			}

			var error_a = json.content.error_a;

			//  erreurs : on n'affiche pas le commentaire.
			// Par contre on affiche les erreurs de remplissage du formulaire
			if ( null !== error_a ) {
				var wrong_field;
				for ( error in error_a ) {
					wrong_field = ($( "<div class = \"form_error\"></div>" ).text( error_a[ error ] ));
					$( wrong_field ).insertBefore( $this.find( '[name=' + error + ']' ) );
				}
			}

			// On rafraîchit les commentaires dans tous les cas : le nouveau commentaire s'affiche s'il est correct.
			refresh_comments();
		},
		error : function(resultat, statut, erreur) {
			console.log(resultat.responseText);
		}
	} );
} );