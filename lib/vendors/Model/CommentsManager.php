<?php
namespace Model;

use \OCFram\Manager;
use \Entity\Comment;



abstract class CommentsManager extends Manager
{
	/**
	 * Méthode permettant d'ajouter un commentaire.
	 * @param $comment Le commentaire à ajouter
	 * @return void
	 */
	abstract protected function InsertCommentc(Comment $comment);
	
	/**
	 * Méthode permettant de supprimer un commentaire.
	 * @param $id L'identifiant du commentaire à supprimer
	 * @return void
	 */
	abstract public function deleteCommentcUsingCommentcId($commentc_id);
	
	/**
	 * Méthode permettant de supprimer tous les commentaires liés à une news
	 * @param $news L'identifiant de la news dont les commentaires doivent être supprimés
	 * @return void
	 */
	abstract public function deleteCommentcUsingNewscId($newsc_id);
	
	/**
	 * Méthode permettant d'enregistrer un commentaire.
	 * @param $comment Le commentaire à enregistrer
	 * @return void
	 */
	public function save(Comment $comment) {

		if ($comment->isValid())
		{
			if ($comment->isNew()) {
				
				if(!is_int($comment->auteur()))
				{
					
					//recuperation du string en id
					$User_manager = new UserManagerPDO($this->dao);
					$user =$User_manager->getUserUsinglogin($comment->auteur());
					if($user==null)
					{
						throw new 	\RuntimeException('le user n\'existe pas');
					}
					$comment->setAuteur($user->id());
					
				}
				$this->InsertCommentc($comment);
			}
			else {
				$this->UpdateCommentc($comment);
			}
		}
		else
		{
			throw new \RuntimeException('Le commentaire doit être validé pour être enregistré');
		}
	}
	
	/**
	 * Méthode permettant de récupérer une liste de commentaires.
	 * @param $news La news sur laquelle on veut récupérer les commentaires
	 * @return array
	 */
	abstract public function getCommentcUsingNewscId($news_id);
	
	/**
	 * Méthode permettant de modifier un commentaire.
	 * @param $comment Le commentaire à modifier
	 * @return void
	 */
	abstract protected function UpdateCommentc(Comment $comment);
	
	/**
	 * Méthode permettant d'obtenir un commentaire spécifique.
	 * @param $id L'identifiant du commentaire
	 * @return Comment
	 */
	abstract public function getCommentcUsingCommentcId($commentc_id);
	
	/*
	 * Récupère tous les commentaires d'une news créés après la date demandée. Construit aussi l'attribut User pour les commentaires écrits par des personnes inscrites.
	 *
	 * @param int $newsc_id
	 * @param string $commentc_datec
	 */
	abstract public function getCommentcUsingNewscIdFilterOverDatecreationSortByIdDesc( $newsc_id, $comment_last );
	
	/*
	 * Récupère tous les commentaires d'une news modifiés après la date demandée.
	 *
	 * @param int $newsc_id
	 * @param string $commentc_dateu
	 */
	abstract public function getCommentcAndUsercUsingNewscIdFilterOverEditedAfterDateupdateAndCreatedBeforeDateupdateSortByIdDesc( $newsc_id, $commentc_dateupdate );
	
	/**
	 * Filtre tous les ids de commentaires qui n'existent pas.
	 *
	 * @param int[] $commentc_id_a
	 *
	 * @return int[]|[]
	 */
	abstract public function filterCommentcUsingUnexistantCommentcId( array $commentc_id_a );
}