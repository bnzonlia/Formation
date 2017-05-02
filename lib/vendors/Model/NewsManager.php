<?php
namespace Model;
use \OCFram\Manager;
use \Entity\News;
abstract class NewsManager extends Manager
{
	/**
	 * Méthode permettant d'ajouter une news.
	 * @param $news News La news à ajouter
	 * @return void
	 */
	abstract protected function InsertNewsc(News $news);
	
	/**
	 * Méthode permettant d'enregistrer une news.
	 * @param $news News la news à enregistrer
	 * @see self::add()
	 * @see self::modify()
	 * @return void
	 */
	public function save(News $news)
	{
		if ($news->isValid())
		{
			$news->isNew() ? $this->InsertNewsc($news) : $this->UpdateNewsc($news);
		}
		else
		{
			throw new \RuntimeException('La news doit être validée pour être enregistrée');
		}
	}
	/**
	 * Méthode renvoyant le nombre de news total.
	 * @return int
	 */
	abstract public function countNewsc();
	/**
	 * Méthode permettant de supprimer une news.
	 * @param $id int L'identifiant de la news à supprimer
	 * @return void
	 */
	abstract public function deleteNewscUsingNewscId($id);
	/**
	 * Méthode retournant une liste de news demandée.
	 * @param $debut int La première news à sélectionner
	 * @param $limite int Le nombre de news à sélectionner
	 * @return array La liste des news. Chaque entrée est une instance de News.
	 */
	abstract public function getNewscAndUserSortByIdDesc($debut = -1, $limite = -1);
	
	/**
	 * Méthode retournant une news précise.
	 * @param $id int L'identifiant de la news à récupérer
	 * @return News La news demandée
	 */
	abstract public function getNewscUsingNewscId($newsc_id);
	/**
	 * Méthode permettant de modifier une news.
	 * @param $news news la news à modifier
	 * @return void
	 */
	abstract protected function UpdateNewsc(News $news);
	
	abstract public function existsNewscUsingNewscId( $newsc_id );
}