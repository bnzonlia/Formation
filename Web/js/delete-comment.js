/**
 * Created by bnzonlia on 25/04/2017.
 */
/**
 * Supprime le commentaire d'id donné de l'affichage
 *
 * @param id
 */
function delete_comment(id) {
	$(".js-comment[data-id="+id+"]").remove();
}