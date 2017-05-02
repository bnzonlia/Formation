/**
 * Created by bnzonlia on 24/04/2017.
 */
/**
 * Ouvre un champ pour éditer le commentaire entré.
 */
$('body').on('click','#js-comment-panel legend a:not(.save)',function( event ) {
	var url = $(this).attr('href');
	var $this = $(this);
	event.preventDefault();
	
	// Requête Ajax
	$.ajax( {
		url      : url,
		type     : "POST",
		dataType : "json",
		success  : function( json, status ) {
			console.log(json);
			if (json.code != 0 ) {
				console.error(json.code);
			}
			else {
				if (typeof json.content.form !== 'undefined') {
					var a = $this.parents(".js-comment").attr('data-id');
					$this.text('Valider la modification ').addClass('save').attr('href', '/admin/comment-save-'+a+'.json');
					
					
					// On affiche le formulaire en retirant les espaces inutiles
					if ($this.parents(".js-comment").find(".js-comment-content").length) {
						$this.parents(".js-comment").find(".js-comment-content").replaceWith($("<div id=\""+$this.parents(".js-comment").attr('data-id')+"\"></div>").html(json.content.form.replace(/[\s\n\r]+/g, ' ')));
					}
					else {
						$this.parents(".js-comment").find("#"+$this.parents(".js-comment").attr('data-id')).replaceWith($("<div id=\""+$this.parents(".js-comment").attr('data-id')+"\"></div>").html(json.content.form.replace(/[\s\n\r]+/g, ' ')));
					}
					// je Rajoute la classe
					$this.parents(".js-comment").find("[name=\"content\"]").attr("class", "js-comment-content-new");
					
				}
				
				else {
					$this.text('Modifier');
					// Supprimer le formulaire
					$this.parents(".js-comment").find("#"+$this.parents(".js-comment").attr('data-id')).remove();
				}
			}
		},
		
		error : function(resultat, statut, erreur) {
			console.log(resultat.responseText);
		}
	});
});




