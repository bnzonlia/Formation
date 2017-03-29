<p style="text-align: center">Il y a actuellement <?= $nombreNews ?> news. En voici la liste :</p>

<table>
	<tr><th>Auteur</th><th>Titre</th><th>Date d'ajout</th><th>Dernière modification</th><th>Action</th></tr>
	<?php
	foreach ($listeNews as $News)
	{
		echo '<tr><td>', $News['user']['login'] , '</td><td>', $News['titre'], '</td><td>le ', $News['dateAjout']->format('d/m/Y à H\hi'), '</td><td>', ($News['dateAjout'] == $News['dateModif'] ? '-' : 'le '.$News['dateModif']->format('d/m/Y à H\hi')), '</td><td><a href="news-update-', $News['id'], '.html"><img src="/images/update.png" alt="Modifier" /></a> <a href="news-delete-', $News['id'], '.html"><img src="/images/delete.png" alt="Supprimer" /></a></td></tr>', "\n";
	}
	?>
</table>
