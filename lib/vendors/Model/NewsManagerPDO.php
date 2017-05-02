<?php
namespace Model;
use \Entity\News;
use \Entity\User;

class NewsManagerPDO extends NewsManager
{
	protected function InsertNewsc(News $news)
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
	public function countNewsc()
	{
		return $this->dao->query('SELECT COUNT(*) FROM news')->fetchColumn();
	}
	public function deleteNewscUsingNewscId($newsc_id)
	{
		$this->dao->exec('DELETE FROM news WHERE id = '.(int) $newsc_id);
	}
	public function getNewscAndUserSortByIdDesc($debut = -1, $limite = -1)
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

		$listeNews_a = [];
		while ( $News = $query->fetch() ) {

			$listeNews_a[] = new News( [
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

		return $listeNews_a;

	}
	
	public function getNewscUsingNewscId($newsc_id)
	{
		/**
		 * @var $stmt \PDOStatement
		 */
		$sql = 'SELECT id, auteur, titre, contenu,dateAjout, dateModif, MMC_id, MMC_login, MMC_password, MMC_datebirth, MMC_firstname, MMC_lastname, MMC_fk_MMY
                FROM news
                	INNER JOIN t_mem_memberc ON auteur = MMC_id
                WHERE id = :id';

		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':id', $newsc_id, \PDO::PARAM_INT );
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

	protected function UpdateNewsc(News $news)
	{
		$requete = $this->dao->prepare('UPDATE news SET titre = :titre, contenu = :contenu, dateModif = NOW() WHERE id = :id');
		
		$requete->bindValue(':titre', $news->titre());
		$requete->bindValue(':contenu', $news->contenu());
		$requete->bindValue(':id', $news->id(), \PDO::PARAM_INT);
		
		$requete->execute();
	}
	
	/**
	 * Vérifie si la news d'id donné existe. Renvoie true si elle existe, false sinon.
	 *
	 * @param $newsc_id int
	 *
	 * @return bool
	 */
	public function existsNewscUsingNewscId( $newsc_id ) {
		$sql = 'SELECT *
				FROM news
				WHERE id = :id';
		
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':id', (int)$newsc_id, \PDO::PARAM_INT );
		$stmt->execute();
		$return = (bool)$stmt->fetch();
		
		$stmt->closeCursor();
		
		return (bool)$return;
	}
}