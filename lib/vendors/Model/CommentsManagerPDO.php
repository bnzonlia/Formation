<?php
namespace Model;

use \Entity\Comment;

class CommentsManagerPDO extends CommentsManager
{
	protected function add(Comment $comment)
	{
		$q = $this->dao->prepare('INSERT INTO comments SET news = :news, auteur = :auteur, contenu = :contenu, date = NOW()');
		
		$q->bindValue(':news', $comment->news(), \PDO::PARAM_INT);
		$q->bindValue(':auteur', $comment->auteur());
		$q->bindValue(':contenu', $comment->contenu());
		
		$q->execute();
		
		$comment->setId($this->dao->lastInsertId());
	}
	
	public function delete($id)
	{
		$this->dao->exec('DELETE FROM comments WHERE id = '.(int) $id);
	}
	
	public function deleteFromNews($news)
	{
		$this->dao->exec('DELETE FROM comments WHERE news = '.(int) $news);
	}
	
	public function getListOf($news)
	{
		$sql = 'SELECT id,news, auteur, contenu,datec, MMC_id, MMC_login, MMC_password, MMC_datebirth, MMC_firstname, MMC_lastname, MMC_fk_MMY
                FROM comments
                	LEFT OUTER JOIN t_mem_memberc ON auteur = MMC_id
                WHERE news = :fk_news
                ORDER BY id DESC';
		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':fk_news', (int)$news, \PDO::PARAM_INT );
		$query->setFetchMode( \PDO::FETCH_ASSOC );
		$query->execute();

		while ( $Comment = $query->fetch() ) {
			$New_comment = new Comment( [
				'id'           => $Comment[ 'id' ],
				'news'       => $Comment[ 'news' ],
				'auteur'       => $Comment[ 'auteur' ],
				'contenu'      => $Comment[ 'contenu' ],
				'datec' => new \DateTime( $Comment[ 'datec' ] ),
			] );
			if ( null != $Comment[ 'MMC_id' ] ) {
				$New_comment->User = new User ( [
					'id'               => $Comment[ 'MMC_id' ],
					'firstname' =>      $Comment[ 'MMC_firstname' ],
					'lastname'    => $Comment[ 'MMC_lastname' ],
					'login'     => $Comment[ 'MMC_login' ],
					'password'           => $Comment[ 'MMC_password' ],
					'datebirth'            =>  new \DateTime( $Comment[ 'MMC_datebirth' ] ),
					'membertype'            => $Comment[ 'MMC_fk_MMY' ],
				] );
			}
			$Liste_comments_a[] = $New_comment;
		}
		var_dump($Liste_comments_a);
		$query->closeCursor();

		return $Liste_comments_a;
	}
	
	protected function modify(Comment $comment)
	{
		$q = $this->dao->prepare('UPDATE comments SET auteur = :auteur, contenu = :contenu WHERE id = :id');
		
		$q->bindValue(':auteur', $comment->auteur());
		$q->bindValue(':contenu', $comment->contenu());
		$q->bindValue(':id', $comment->id(), \PDO::PARAM_INT);
		
		$q->execute();
	}
	
	public function get($id)
	{
		$q = $this->dao->prepare('SELECT id, news, auteur, contenu FROM comments WHERE id = :id');
		$q->bindValue(':id', (int) $id, \PDO::PARAM_INT);
		$q->execute();
		
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');
		
		return $q->fetch();
	}
}