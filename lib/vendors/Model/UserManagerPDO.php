<?php
namespace Model;
use \Entity\User;
class UserManagerPDO extends UserManager
{


	protected function insertUserc(User $user)
	{
		$requete = $this->dao->prepare('INSERT INTO t_mem_memberc SET  MMC_firstname = :MMC_firstname, MMC_lastname = :MMC_lastname, MMC_login = :MMC_login, MMC_password = :MMC_password , MMC_datebirth = NOW()');
		
		$requete->bindValue(':MMC_firstname', $user->firstname());
		$requete->bindValue(':MMC_lastname', $user->lastname());
		$requete->bindValue(':MMC_login', $user->login());
		$requete->bindValue(':MMC_password', $user->password());
		$requete->execute();
	}
	public function addInvite(User $user)
	{
		$requete = $this->dao->prepare('INSERT INTO t_mem_memberc SET  MMC_login = :MMC_login,MMC_fk_MMY = :MMC_fk_MMY, MMC_datebirth = NOW()');

		$requete->bindValue(':MMC_login', $user->login());
		$requete->bindValue(':MMC_fk_MMY', $user->membertype());
		$requete->execute();
	}
	public function countUserc()
	{
		$requete= $this->dao->prepare('SELECT COUNT(*) FROM t_mem_memberc')->fetchColumn();
		$requete->execute();
		return $requete;
	}
	public function deleteUsercUsingUsercId($Userc_id)
	{
		$requete = $this->dao->prepare('DELETE FROM t_mem_memberc WHERE MMC_id = '.(int) $Userc_id);
		$requete->execute();
		
	}
	public function getUsercSortByIdDesc($debut = -1, $limite = -1)
	{
		$sql = 'SELECT MMC_id as id, MMC_firstname as firstname, MMC_lastname as lastname, MMC_login as login, MMC_password as password, MMC_datebirth  as datebirth, MMC_fk_MMY as membertype FROM t_mem_memberc ORDER BY MMC_id DESC';
		
		if ($debut != -1 || $limite != -1)
		{
			$sql .= ' LIMIT '.(int) $limite.' OFFSET '.(int) $debut;
		}
		
		$requete = $this->dao->prepare( $sql );
		$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');
		$requete->execute();
		
		$listeUser = $requete->fetchAll();
		
		foreach ($listeUser as $user)
		{
			$user->setDateBirth(new \DateTime($user->datebirth()));
		}
		
		$requete->closeCursor();
		
		return $listeUser;
	}
	
	public function getUsercUsingUsercId($Userc_id)
	{
		$requete = $this->dao->prepare('SELECT MMC_id as id, MMC_firstname as firstname, MMC_lastname as lastname, MMC_login as login, MMC_password as password, MMC_datebirth  as datebirth FROM t_mem_memberc WHERE MMC_id = :MMC_id');
		$requete->bindValue(':MMC_id', (int) $Userc_id, \PDO::PARAM_INT);
		$requete->execute();
		
		$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');
		
		if ($user = $requete->fetch())
		{
			$user->setDateBirth(new \DateTime($user->datebirth()));
			
			return $user;
		}
		
		return null;
	}
	protected function UpdateUserc(User $user)
	{
		$requete = $this->dao->prepare('UPDATE t_mem_memberc SET  MMC_firstname = :MMC_firstname, MMC_lastname = :MMC_lastname, MMC_login = :MMC_login,MMC_password = SHA1(:MMC_password) ,MMC_datebirth = NOW() WHERE MMC_id = :MMC_id');
		
		$requete->bindValue(':MMC_firstname', $user->firstname());
		$requete->bindValue(':MMC_lastname', $user->lastname());
		$requete->bindValue(':MMC_login', $user->login());
		$requete->bindValue(':MMC_password', $user->password());
		$requete->bindValue(':MMC_id', $user->id(), \PDO::PARAM_INT);
		
		$requete->execute();
	}

	/**
	 * recuperer un user grace a son login
	 * @param string $login
	 *
	 * @return User
	 */
	public function getUserUsingLogin($login)
	{
		$sql= $this->dao->prepare('SELECT MMC_id as id, MMC_firstname as firstname, MMC_lastname as lastname, MMC_login as login, MMC_password as password, MMC_datebirth  as datebirth , MMC_fk_MMY as membertype FROM  t_mem_memberc WHERE MMC_login = :MMC_login');
		$sql->bindvalue(':MMC_login',(string) $login);
		$sql->execute();
		$sql->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');
		
		if ($user = $sql->fetch())
		{
			$user->setDateBirth(new \DateTime($user->datebirth()));
			
			return $user;
		}
		return null;
	}

	/**
	 * recupere un user grace a son login qui n'ai pas un invite
	 * @param $login
	 *
	 * @return null
	 */
	public function getUserWithoutGuestUsingLogin($login)
	{
		$sql= $this->dao->prepare('SELECT MMC_id as id, MMC_firstname as firstname, MMC_lastname as lastname, MMC_login as login, MMC_password as password, MMC_datebirth  as datebirth , MMC_fk_MMY as membertype
 									FROM  t_mem_memberc
 									WHERE MMC_login = :MMC_login AND MMC_fk_MMY!= :MMC_fk_MMY');

		$sql->bindvalue(':MMC_login',(string) $login);
		$sql->bindvalue(':MMC_fk_MMY', UserManager::MMY_INVITE);
		$sql->execute();
		$sql->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');

		if ($user = $sql->fetch())
		{
			$user->setDateBirth(new \DateTime($user->datebirth()));

			return $user;
		}
		return null;
	}
	
	/**
	 * Vérifie si l'utilisateur de pseudo donné existe déjà.
	 *
	 * @param string $userc_login
	 *
	 * @return bool
	 */
	public function existsUserUsingLogin($login) {
		$sql = 'SELECT *
				FROM T_mem_memberc
				WHERE 	MMC_login = :login';
		$stmt = $this->dao->prepare($sql);
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		return (bool)$stmt->fetch();
	}
	
	/**
	 * recuperer un user grace a son id
	 * @param string $login
	 *
	 * @return User
	 */
	public function getUserUsingId($id)
	{
		$sql= $this->dao->prepare('SELECT MMC_id as id, MMC_firstname as firstname, MMC_lastname as lastname, MMC_login as login, MMC_password as password, MMC_datebirth  as datebirth , MMC_fk_MMY as membertype FROM  t_mem_memberc WHERE MMC_id = :MMC_id');
		$sql->bindvalue(':MMC_id',(string) $id);
		$sql->execute();
		$sql->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');
		
		if ($user = $sql->fetch())
		{
			$user->setDateBirth(new \DateTime($user->datebirth()));
			
			return $user;
		}
		return null;
	}
}