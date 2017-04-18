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
		$sql = 'SELECT id, news, auteur, contenu, datec ,MMC_id,MMC_firstname,MMC_lastname,MMC_login,MMC_password,MMC_datebirth,MMC_fk_MMY
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
	
	protected function UpdateCommentc(Comment $comment)
	{
		$q = $this->dao->prepare('UPDATE comments SET contenu = :contenu ,dateu = NOW() WHERE id = :id');

		$q->bindValue(':contenu', $comment->contenu());
		$q->bindValue(':id', $comment->id(), \PDO::PARAM_INT);
		
		$q->execute();
	}
	
	public function getCommentcUsingCommentcId($commentc_id)
	{
		$q = $this->dao->prepare('SELECT id, news, auteur, contenu FROM comments WHERE id = :id');
		$q->bindValue(':id', (int) $commentc_id, \PDO::PARAM_INT);
		$q->execute();
		
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');
		
		return $q->fetch();
	}

}