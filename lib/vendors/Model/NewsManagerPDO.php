<?php
namespace Model;
use \Entity\News;
use \Entity\User;

class NewsManagerPDO extends NewsManager
{
	protected function add(News $news)
	{
		/**
		 * @var $stmt  \PDOStatement
		 * @var $News  News
		 */
		$sql = 'INSERT INTO news (auteur,titre,contenu,dateAjout,dateModif)
                    VALUES (:auteur,:titre,:contenu,NOW(),NOW())';

		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':auteur', $news->auteur(), \PDO::PARAM_INT );
		$stmt->bindValue( ':titre', $news->titre(), \PDO::PARAM_STR );
		$stmt->bindValue( ':contenu', $news->contenu(), \PDO::PARAM_STR );
		$stmt->execute();
	}
	public function count()
	{
		return $this->dao->query('SELECT COUNT(*) FROM news')->fetchColumn();
	}
	public function delete($id)
	{
		$this->dao->exec('DELETE FROM news WHERE id = '.(int) $id);
	}
	public function getList($debut = -1, $limite = -1)
	{
		$sql = 'SELECT id, auteur, titre, contenu, dateAjout, dateModif ,MMC_id,MMC_firstname,MMC_lastname,MMC_login,MMC_password,MMC_datebirth,MMC_fk_MMY
									FROM news
									INNER JOIN t_mem_memberc ON auteur=MMC_id
									ORDER BY id DESC';


		if ($debut != -1 || $limite != -1)
		{
			$sql .= ' LIMIT '.(int) $limite.' OFFSET '.(int) $debut;
		}
		$query = $this->dao->prepare( $sql );
		$query->setFetchMode( \PDO::FETCH_ASSOC );
		$query->execute();

		$listeNews = [];
		while ( $News = $query->fetch() ) {

			$listeNews[] = new News( [
				'id'         => (int)$News[ 'id' ],
				'auteur'     => (int)$News[ 'auteur' ],
				'titre'      => $News[ 'titre' ],
				'contenu'    => $News[ 'contenu' ],
				'dateAjout'  => new \DateTime( $News[ 'dateAjout' ] ),
				'dateModif'	 => new \DateTime( $News[ 'dateModif' ] ),
				'user'		 => new User ( [
						'id'         => (int)$News[ 'MMC_id' ],
						'firstname'     => $News[ 'MMC_firstname' ],
						'lastname'      => $News[ 'MMC_lastname' ],
						'login'    => $News[ 'MMC_login' ],
						'password'  => $News[ 'MMC_password' ],
						'datebirth'	 => new \DateTime( $News[ 'MMC_datebirth' ] ),
						'membertype' => $News[ 'MMC_fk_MMY' ]
					]
				)
			] );
		}
		$query->closeCursor();

		return $listeNews;

	}
	
	public function getUnique($id)
	{
		/**
		 * @var $stmt \PDOStatement
		 */
		$sql = 'SELECT id, auteur, titre, contenu,dateAjout, dateModif, MMC_id, MMC_login, MMC_password, MMC_datebirth, MMC_firstname, MMC_lastname, MMC_fk_MMY
                FROM news
                	INNER JOIN t_mem_memberc ON auteur = MMC_id
                WHERE id = :id';

		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':id', $id, \PDO::PARAM_INT );
		$query->execute();

		if ( $result = $query->fetch( \PDO::FETCH_ASSOC ) ) {
			$News = new News( [
				'id'         => (int)$result[ 'id' ],
				'auteur'     => (int)$result[ 'auteur' ],
				'titre'      => $result[ 'titre' ],
				'contenu'    => $result[ 'contenu' ],
				'dateAjout'  => new \DateTime( $result[ 'dateAjout' ] ),
				'dateModif'	 => new \DateTime( $result[ 'dateModif' ] ),
				'user'		 => new User ( [
						'id'         => (int)$result[ 'MMC_id' ],
						'firstname'     => $result[ 'MMC_firstname' ],
						'lastname'      => $result[ 'MMC_lastname' ],
						'login'    => $result[ 'MMC_login' ],
						'password'  => $result[ 'MMC_password' ],
						'datebirth'	 => new \DateTime( $result[ 'MMC_datebirth' ] ),
						'membertype' => $result[ 'MMC_fk_MMY' ]
					]
				)
			] );
		}
		else {
			$News = null;
		}
		$query->closeCursor();

		return $News;

	}

	protected function modify(News $news)
	{
		$requete = $this->dao->prepare('UPDATE news SET titre = :titre, contenu = :contenu, dateModif = NOW() WHERE id = :id');
		
		$requete->bindValue(':titre', $news->titre());
		$requete->bindValue(':contenu', $news->contenu());
		$requete->bindValue(':id', $news->id(), \PDO::PARAM_INT);
		
		$requete->execute();
	}
}