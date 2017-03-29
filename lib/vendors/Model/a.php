<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:35
 */
namespace Model;
use Entity\Comment;
use Entity\User;
use OCFram\Router;
/**
 * Class CommentsManagerPDO
 *
 * Implémentation d'un CommentsManager avec la bibliothèque PDO pour Mysql.
 *
 * @package Model
 */
class CommentsManagerPDO extends CommentsManager {
	/**
	 * Insère le commentaire en DB.
	 * Cette méthode ne doit pas être appelée directement ; utiliser la méthode publique save.
	 *
	 * @param Comment $Comment
	 */
	protected function insertCommentc( Comment $Comment ) {
		/**
		 * @var $stmt    \PDOStatement
		 * @var $Comment Comment
		 */
		$sql = 'INSERT INTO T_SIT_commentc
                    (SCC_fk_SNC, SCC_author, SCC_content, SCC_datecreation, SCC_dateupdate)
                VALUES (:fk_SNC, :author, :content, NOW(), NOW())';

		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':fk_SNC', $Comment->fk_SNC(), \PDO::PARAM_INT );
		$stmt->bindValue( ':author', $Comment->author(), \PDO::PARAM_STR );
		$stmt->bindValue( ':content', $Comment->content(), \PDO::PARAM_STR );
		$stmt->execute();
		$Comment->setId( $this->dao->lastInsertId() );
	}

	/**
	 * Modifie le commentaire en DB (contenu et date de mise à jour)
	 * Cette méthode ne doit pas être appelée directement ; utiliser la méthode publique save.
	 *
	 * @param Comment $Comment
	 */
	protected function updateCommentc( Comment $Comment ) {
		/**
		 * @var $stmt    \PDOStatement
		 * @var $Comment Comment
		 */
		$sql  = 'UPDATE T_SIT_commentc
                SET SCC_content = :content,
                	SCC_dateupdate = NOW()
                WHERE SCC_id = :id';
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':content', $Comment->content(), \PDO::PARAM_STR );
		$stmt->bindValue( ':id', $Comment->id(), \PDO::PARAM_INT );
		$stmt->execute();
	}

	/**
	 * Récupère tous les commentaires associés à la news d'id passé en paramètre. Construit aussi l'attribut User pour les commentaires écrits par des personnes inscrites.
	 *
	 * @param $newsc_id int ID de la news
	 *
	 * @return Comment[]
	 */
	public function getCommentcAndUsercUsingNewscIdSortByNewscIdDesc( $newsc_id ) {
		/**
		 * @var $stmt             \PDOStatement
		 * @var $Liste_comments_a Comment[]
		 */
		$sql = 'SELECT SCC_id, SCC_fk_SNC, SCC_author, SCC_content, SCC_datecreation, SCC_dateupdate,
					SUC_id, SUC_datesubscription, SUC_fk_SUE_banned, SUC_fk_SUE_valid, SUC_fk_SUY, SUC_login, SUC_email
                FROM T_SIT_commentc
                	LEFT OUTER JOIN t_sit_userc ON SCC_author = SUC_login
                WHERE SCC_fk_SNC = :fk_SNC
                ORDER BY SCC_id DESC';

		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':fk_SNC', (int)$newsc_id, \PDO::PARAM_INT );
		$stmt->setFetchMode( \PDO::FETCH_ASSOC );
		$stmt->execute();
		$Liste_comments_a = [];
		while ( $Comment = $stmt->fetch() ) {
			$New_comment = new Comment( [
				'id'           => $Comment[ 'SCC_id' ],
				'fk_SNC'       => $Comment[ 'SCC_fk_SNC' ],
				'author'       => $Comment[ 'SCC_author' ],
				'content'      => $Comment[ 'SCC_content' ],
				'datecreation' => new \DateTime( $Comment[ 'SCC_datecreation' ] ),
				'dateupdate'   => new \DateTime( $Comment[ 'SCC_dateupdate' ] ),
			] );
			if ( null != $Comment[ 'SUC_id' ] ) {
				$New_comment->User = new User ( [
					'id'               => $Comment[ 'SUC_id' ],
					'datesubscription' => new \DateTime( $Comment[ 'SUC_datesubscription' ] ),
					'fk_SUE_banned'    => $Comment[ 'SUC_fk_SUE_banned' ],
					'fk_SUE_valid'     => $Comment[ 'SUC_fk_SUE_valid' ],
					'fk_SUY'           => $Comment[ 'SUC_fk_SUY' ],
					'login'            => $Comment[ 'SUC_login' ],
					'email'            => $Comment[ 'SUC_email' ],
				] );
			}
			$Liste_comments_a[] = $New_comment;
		}
		$stmt->closeCursor();

		return $Liste_comments_a;
	}

	/**
	 * Récupère le commentaire d'id donné.
	 *
	 * @param $commentc_id int ID du commentaire
	 *
	 * @return Comment|null
	 */
	public function getCommentcUsingCommentcId( $commentc_id ) {
		/**
		 * @var         $stmt \PDOStatement
		 * @var Comment $Comment
		 */
		$sql  = 'SELECT SCC_id id, SCC_fk_SNC fk_SNC, SCC_author author, SCC_content content, SCC_datecreation datecreation, SCC_dateupdate dateupdate
                FROM T_SIT_commentc
                WHERE SCC_id = :id';
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':id', (int)$commentc_id, \PDO::PARAM_INT );
		$stmt->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\Comment' ); // Incohérence avec la doc PHP
		$stmt->execute();
		$Comment = $stmt->fetch();
		if ( $Comment != null ) {
			$Comment->setDatecreation( new \DateTime( $Comment->datecreation() ) );
			$Comment->setDateupdate( new \DateTime( $Comment->dateupdate() ) );
		}
		$stmt->closeCursor();

		return $Comment;
	}

	/**
	 * Supprime le commentaire d'id fourni en paramètre.
	 *
	 * @param $commentc_id int ID du commentaire
	 */
	public function deleteCommentcUsingCommentcId( $commentc_id ) {
		/**
		 * @var $stmt \PDOStatement
		 */
		$sql  = 'DELETE FROM T_SIT_commentc
                WHERE SCC_id = :id';
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':id', (int)$commentc_id, \PDO::PARAM_INT );
		$stmt->execute();
	}

	/**
	 * Supprime tous les commentaires liés à une news d'id donné.
	 *
	 * @param $newsc_id int ID de la news
	 */
	public function deleteCommentcUsingNewscId( $newsc_id ) {
		/**
		 * @var $stmt \PDOStatement
		 */
		$sql  = 'DELETE FROM T_SIT_commentc
                WHERE SCC_fk_SNC = :fk_SNC';
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':fk_SNC', (int)$newsc_id, \PDO::PARAM_INT );
		$stmt->execute();
	}

	/**
	 * Vérifie si le commentaire d'id donné existe en base.
	 *
	 * @param $commentc_id int
	 *
	 * @return bool
	 */
	public function existsCommentcUsingCommentcId( $commentc_id ) {
		$sql = 'SELECT *
				FROM T_SIT_commentc
				WHERE SCC_id = :id';

		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':id', (int)$commentc_id, \PDO::PARAM_INT );
		$stmt->execute();
		$return = (bool)$stmt->fetch();
		$stmt->closeCursor();

		return (bool)$return;
	}

	/*
	 * Récupère tous les commentaires d'une news créés après la date demandée. Construit aussi l'attribut User pour les commentaires écrits par des personnes inscrites.
	 *
	 * @param int $newsc_id
	 * @param string $commentc_datecreation
	 */
	public function getCommentcUsingNewscIdFilterOverDatecreationSortByIdDesc( $newsc_id, $commentc_datecreation ) {
		/**
		 * @var $stmt      \PDOStatement
		 * @var $Comment_a Comment[]
		 */
		$sql = 'SELECT SCC_id, SCC_fk_SNC, SCC_author, SCC_content, SCC_datecreation, SCC_dateupdate,
					SUC_id, SUC_datesubscription, SUC_fk_SUE_valid, SUC_email, SUC_login, SUC_fk_SUY, SUC_fk_SUE_banned
                FROM T_SIT_commentc
                	LEFT OUTER JOIN t_sit_userc ON SCC_author = SUC_login
                WHERE SCC_fk_SNC = :fk_SNC
                	AND SCC_datecreation > :datecreation
				ORDER BY SCC_id DESC';

		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':fk_SNC', (int)$newsc_id, \PDO::PARAM_INT );
		$stmt->bindValue( ':datecreation', $commentc_datecreation );
		$stmt->setFetchMode( \PDO::FETCH_ASSOC );
		$stmt->execute();
		$Comment_a = [];
		while ( $Comment = $stmt->fetch() ) {
			$New_comment = new Comment( [
				'id'           => $Comment[ 'SCC_id' ],
				'fk_SNC'       => $Comment[ 'SCC_fk_SNC' ],
				'author'       => $Comment[ 'SCC_author' ],
				'content'      => $Comment[ 'SCC_author' ],
				'datecreation' => new \DateTime( $Comment[ 'SCC_datecreation' ] ),
				'dateupdate'   => new \DateTime( $Comment[ 'SCC_dateupdate' ] ),
			] );
			if ( null != $Comment[ 'SUC_id' ] ) {
				$New_comment->User = new User ( [
					'id'               => $Comment[ 'SUC_id' ],
					'datesubscription' => new \DateTime( $Comment[ 'SUC_datesubscription' ] ),
					'fk_SUE_banned'    => $Comment[ 'SUC_fk_SUE_banned' ],
					'fk_SUE_valid'     => $Comment[ 'SUC_fk_SUE_valid' ],
					'fk_SUY'           => $Comment[ 'SUC_fk_SUY' ],
					'login'            => $Comment[ 'SUC_login' ],
					'email'            => $Comment[ 'SUC_email' ],
				] );
			}
			$Comment_a[] = $New_comment;
		}
		$stmt->closeCursor();

		return $Comment_a;
	}

	/*
	 * Récupère tous les commentaires d'une news modifiés après la date demandée et créés avant cette même date.
	 *
	 * @param int $newsc_id
	 * @param string $commentc_dateupdate
	 */
	public function getCommentcAndUsercUsingNewscIdFilterOverEditedAfterDateupdateAndCreatedBeforeDateupdateSortByIdDesc( $newsc_id, $commentc_dateupdate ) {
		/**
		 * @var $stmt      \PDOStatement
		 * @var $Comment_a Comment[]
		 */
		$sql = 'SELECT SCC_id, SCC_fk_SNC, SCC_author, SCC_content, SCC_datecreation, SCC_dateupdate,
					SUC_id, SUC_datesubscription, SUC_fk_SUE_valid, SUC_email, SUC_login, SUC_fk_SUY, SUC_fk_SUE_banned
                FROM T_SIT_commentc
                	LEFT OUTER JOIN t_sit_userc ON SUC_login = SCC_author
                WHERE SCC_fk_SNC = :fk_SNC
                	AND SCC_dateupdate > :dateupdate
                	AND SCC_datecreation <= :dateupdate
				ORDER BY SCC_id DESC';

		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':fk_SNC', (int)$newsc_id, \PDO::PARAM_INT );
		$stmt->bindValue( ':dateupdate', $commentc_dateupdate );
		$stmt->setFetchMode( \PDO::FETCH_ASSOC);
		$stmt->execute();
		$Comment_a = [];
		while ( $Comment = $stmt->fetch() ) {
			$New_comment = new Comment( [
				'id'           => $Comment[ 'SCC_id' ],
				'fk_SNC'       => $Comment[ 'SCC_fk_SNC' ],
				'author'       => $Comment[ 'SCC_author' ],
				'content'      => $Comment[ 'SCC_content' ],
				'datecreation' => new \DateTime( $Comment[ 'SCC_datecreation' ] ),
				'dateupdate'   => new \DateTime( $Comment[ 'SCC_dateupdate' ] ),
			] );
			if ( null != $Comment[ 'SUC_id' ] ) {
				$New_comment->User = new User ( [
					'id'               => $Comment[ 'SUC_id' ],
					'datesubscription' => new \DateTime( $Comment[ 'SUC_datesubscription' ] ),
					'fk_SUE_banned'    => $Comment[ 'SUC_fk_SUE_banned' ],
					'fk_SUE_valid'     => $Comment[ 'SUC_fk_SUE_valid' ],
					'fk_SUY'           => $Comment[ 'SUC_fk_SUY' ],
					'login'            => $Comment[ 'SUC_login' ],
					'email'            => $Comment[ 'SUC_email' ],
				] );
			}
			$Comment_a[] = $New_comment;
		}
		$stmt->closeCursor();

		return $Comment_a;
	}

	/**
	 * Filtre tous les ids de commentaires qui n'existent pas en base.
	 *
	 * @param int[] $commentc_id_a
	 *
	 * @return int[]|[]
	 */
	public function filterCommentcUsingUnexistantCommentcId( array $commentc_id_a ) {
		// Générer des "?" pour créer l'ensemble de nos ids sélectionnés
		// On sélectionne tous les ids de la base qui sont dans l'ensemble des ids reçus
		$q_marks_for_query = implode( ',', array_fill( 0, count( $commentc_id_a ), '?' ) );
		$sql               = 'SELECT SCC_id
				FROM t_sit_commentc
				WHERE SCC_id IN (' . $q_marks_for_query . ')';
		$stmt              = $this->dao->prepare( $sql );
		foreach ( $commentc_id_a as $number => $id ) {
			$stmt->bindValue( $number + 1, $id );
		}
		$stmt->execute();

		$existant_ids_a = [];
		while ( $id = $stmt->fetchColumn() ) {
			$existant_ids_a[] = (int)$id;
		}
		$stmt->closeCursor();

		return array_diff( $commentc_id_a, $existant_ids_a );
	}

	/**
	 * Récupère tous les commentaires d'un User donné par son login
	 *
	 * @param string $userc_login
	 *
	 * @return Comment[]|[]
	 */
	public function getCommentcUsingUsercLoginSortByFk_SNCDesc( $userc_login ) {
		/**
		 * @var Comment $Comment
		 */
		$sql = 'SELECT SCC_id id, SCC_author author, SCC_dateupdate dateupdate, SCC_datecreation datecreation, SCC_content content, SCC_fk_SNC fk_SNC
				FROM t_sit_commentc
				WHERE SCC_author = :login
				ORDER BY SCC_id DESC';

		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( 'login', $userc_login );
		$stmt->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\Comment' );
		$stmt->execute();
		$Comment_a = [];
		while ( $Comment = $stmt->fetch() ) {
			$Comment->setDatecreation( new \DateTime( $Comment->datecreation() ) );
			$Comment->setDateupdate( new \DateTime( $Comment->dateupdate() ) );
			$Comment_a[] = $Comment;
		}

		return $Comment_a;
	}
}