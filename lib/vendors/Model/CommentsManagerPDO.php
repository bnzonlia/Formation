<?php
namespace Model;

use \Entity\Comment;
use Entity\User;

class CommentsManagerPDO extends CommentsManager
{
	protected function InsertCommentc(Comment $comment)
	{
		$sql = 'INSERT INTO comments
                    (news, auteur, contenu,datec,dateu)
                VALUES (:news, :auteur, :contenu, NOW(), NOW())';

		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':news', $comment->news(), \PDO::PARAM_INT );
		$stmt->bindValue( ':auteur', $comment->auteur(), \PDO::PARAM_INT );
		$stmt->bindValue( ':contenu', $comment->contenu(), \PDO::PARAM_STR );
		$stmt->execute();
		$comment->setId( $this->dao->lastInsertId() );
	}
	
	public function deleteCommentcUsingCommentcId($commentc_id)
	{
		$sql = 'DELETE FROM comments WHERE id = '.(int) $commentc_id;
		$stmt = $this->dao->prepare( $sql );
		$stmt->execute();
	}
	
	public function deleteCommentcUsingNewscId($newsc_id)
	{
		$sql = 'DELETE FROM comments WHERE news = '.(int) $newsc_id;
		$stmt = $this->dao->prepare( $sql );
		$stmt->execute();
	}
	
	public function getCommentcUsingNewscId($newsc_id)
	{
		$sql = 'SELECT id, news,auteur,contenu,datec,dateu ,MMC_id,MMC_firstname,MMC_lastname,MMC_login,MMC_password,MMC_datebirth,MMC_fk_MMY
                FROM comments
                	LEFT OUTER JOIN t_mem_memberc ON auteur = MMC_id
                	WHERE news = :news
                	ORDER BY id DESC';

		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':news', (int)$newsc_id, \PDO::PARAM_INT );
		$stmt->setFetchMode( \PDO::FETCH_ASSOC );
		$stmt->execute();
		$Liste_comments_a = [];
		while ( $Comment = $stmt->fetch() ) {
			$New_comment = new Comment( [
				'id'           => $Comment[ 'id' ],
				'news'       => $Comment[ 'news' ],
				'auteur'       => $Comment[ 'auteur' ],
				'contenu'      => $Comment[ 'contenu' ],
				'datec' => new \DateTime( $Comment[ 'datec' ] ),
				'dateu' =>new \DateTime( $Comment[ 'dateu' ] ),
			] );
			if ( null != $Comment[ 'MMC_id' ] ) {
				$New_comment->User = new User ( [
					'id'         => (int)$Comment[ 'MMC_id' ],
					'firstname'     => $Comment[ 'MMC_firstname' ],
					'lastname'      => $Comment[ 'MMC_lastname' ],
					'login'    => $Comment[ 'MMC_login' ],
					'password'  => $Comment[ 'MMC_password' ],
					'datebirth'	 => new \DateTime( $Comment[ 'MMC_datebirth' ] ),
					'membertype' => $Comment[ 'MMC_fk_MMY' ],
				] );
			}
			$Liste_comments_a[] = $New_comment;

		}

		$stmt->closeCursor();

		return $Liste_comments_a;

	}

	public function getCommentcUsingNewscIdFilterOverDatecreationSortByIdDesc($newsc_id,$comment_last)
	{
		$sql = 'SELECT id, news, auteur, contenu, datec ,MMC_id,MMC_firstname,MMC_lastname,MMC_login,MMC_password,MMC_datebirth,MMC_fk_MMY
                FROM comments
                	LEFT OUTER JOIN t_mem_memberc ON auteur = MMC_id
                WHERE news = :news
                AND id > :id
                ORDER BY id  ';

		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':news', (int)$newsc_id, \PDO::PARAM_INT );
		$stmt->bindValue( ':id', $comment_last );
		$stmt->setFetchMode( \PDO::FETCH_ASSOC );
		$stmt->execute();
		$Liste_comments_a = [];
		while ( $Comment = $stmt->fetch() ) {
			$New_comment = new Comment( [
				'id'           => $Comment[ 'id' ],
				'news'       => $Comment[ 'news' ],
				'auteur'       => $Comment[ 'auteur' ],
				'contenu'      => $Comment[ 'contenu' ],
				'datec' => new \DateTime( $Comment[ 'datec' ] ),
				'dateu' =>new \DateTime( $Comment[ 'datec' ] ),
			] );
			if ( null != $Comment[ 'MMC_id' ] ) {
				$New_comment->User = new User ( [
					'id'         => (int)$Comment[ 'MMC_id' ],
					'firstname'     => $Comment[ 'MMC_firstname' ],
					'lastname'      => $Comment[ 'MMC_lastname' ],
					'login'    => $Comment[ 'MMC_login' ],
					'password'  => $Comment[ 'MMC_password' ],
					'datebirth'	 => new \DateTime( $Comment[ 'MMC_datebirth' ] ),
					'membertype' => $Comment[ 'MMC_fk_MMY' ],
				] );
			}
			$Liste_comments_a[] = $New_comment;

		}

		$stmt->closeCursor();

		return $Liste_comments_a;

	}

	public function getCommentcAndUsercUsingNewscIdFilterOverEditedAfterDateupdateAndCreatedBeforeDateupdateSortByIdDesc( $newsc_id, $commentc_dateu )
	{
		$sql = 'SELECT id, news, auteur, contenu, datec,dateu,MMC_id,MMC_firstname,MMC_lastname,MMC_login,MMC_password,MMC_datebirth,MMC_fk_MMY
                FROM comments
                	LEFT OUTER JOIN t_mem_memberc ON auteur = MMC_id
                WHERE news = :news
                AND dateu > :dateu
                	AND datec <= :dateu
                ORDER BY id DESC';

		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':news', (int)$newsc_id, \PDO::PARAM_INT );
		$stmt->bindValue( ':dateu', $commentc_dateu );
		$stmt->setFetchMode( \PDO::FETCH_ASSOC );
		$stmt->execute();
		$Liste_comments_a = [];
		while ( $Comment = $stmt->fetch() ) {
			$New_comment = new Comment( [
				'id'           => $Comment[ 'id' ],
				'news'       => $Comment[ 'news' ],
				'auteur'       => $Comment[ 'auteur' ],
				'contenu'      => $Comment[ 'contenu' ],
				'datec' => new \DateTime( $Comment[ 'datec' ] ),
				'dateu' =>new \DateTime( $Comment[ 'datec' ] ),
			] );
			if ( null != $Comment[ 'MMC_id' ] ) {
				$New_comment->User = new User ( [
					'id'         => (int)$Comment[ 'MMC_id' ],
					'firstname'     => $Comment[ 'MMC_firstname' ],
					'lastname'      => $Comment[ 'MMC_lastname' ],
					'login'    => $Comment[ 'MMC_login' ],
					'password'  => $Comment[ 'MMC_password' ],
					'datebirth'	 => new \DateTime( $Comment[ 'MMC_datebirth' ] ),
					'membertype' => $Comment[ 'MMC_fk_MMY' ],
				] );
			}
			$Liste_comments_a[] = $New_comment;

		}

		$stmt->closeCursor();

		return $Liste_comments_a;

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
		$sql               = 'SELECT id
				FROM comments
				WHERE id IN (' . $q_marks_for_query . ')';
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
	
	protected function UpdateCommentc(Comment $comment)
	{
		$q = $this->dao->prepare('UPDATE comments SET contenu = :contenu ,dateu = NOW() WHERE id = :id');

		$q->bindValue(':contenu', $comment->contenu());
		$q->bindValue(':id', $comment->id(), \PDO::PARAM_INT);
		
		$q->execute();
	}
	
	public function getCommentcUsingCommentcId($commentc_id)
	{
		$q = $this->dao->prepare('SELECT id, news, auteur, contenu,datec,dateu FROM comments WHERE id = :id');
		$q->bindValue(':id', (int) $commentc_id, \PDO::PARAM_INT);
		$q->execute();
		
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');
		$Comment=$q->fetch();
		if ( $Comment != null ) {
			$Comment->setDatec( new \DateTime( $Comment->datec() ) );
			$Comment->setDateu( new \DateTime( $Comment->dateu() ) );
		}
		$q->closeCursor();
		return $Comment;
	}

}