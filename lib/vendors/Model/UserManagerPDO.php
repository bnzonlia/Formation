<?php
namespace Model;
use \Entity\User;
class UserManagerPDO extends UserManager
{
	protected function add(User $user)
	{
		$requete = $this->dao->prepare('INSERT INTO t_mem_memberc SET  MMC_firstname = :MMC_firstname, MMC_lastname = :MMC_lastname, MMC_login = :MMC_login, MMC_password = SHA1(:MMC_password) , MMC_datebirth = NOW()');
		
		$requete->bindValue(':MMC_firstname', $user->firstname());
		$requete->bindValue(':MMC_lastname', $user->lastname());
		$requete->bindValue(':MMC_login', $user->login());
		$requete->bindValue(':MMC_password', $user->password());
		$requete->execute();
	}
	public function count()
	{
		return $this->dao->query('SELECT COUNT(*) FROM t_mem_memberc')->fetchColumn();
	}
	public function delete($id)
	{
		$this->dao->exec('DELETE FROM t_mem_memberc WHERE MMC_id = '.(int) $id);
	}
	public function getList($debut = -1, $limite = -1)
	{
		$sql = 'SELECT MMC_id as id, MMC_firstname as firstname, MMC_lastname as lastname, MMC_login as login, MMC_password as password, MMC_datebirth  as datebirth FROM t_mem_memberc ORDER BY MMC_id DESC';
		
		if ($debut != -1 || $limite != -1)
		{
			$sql .= ' LIMIT '.(int) $limite.' OFFSET '.(int) $debut;
		}
		
		$requete = $this->dao->query($sql);
		$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');
		
		$listeUser = $requete->fetchAll();
		
		foreach ($listeUser as $user)
		{
			$user->setDateBirth(new \DateTime($user->datebirth()));
		}
		
		$requete->closeCursor();
		
		return $listeUser;
	}
	
	public function getUnique($id)
	{
		$requete = $this->dao->prepare('SELECT MMC_id as id, MMC_firstname as firstname, MMC_lastname as lastname, MMC_login as login, MMC_password as password, MMC_datebirth  as datebirth FROM t_mem_memberc WHERE MMC_id = :MMC_id');
		$requete->bindValue(':MMC_id', (int) $id, \PDO::PARAM_INT);
		$requete->execute();
		
		$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');
		
		if ($user = $requete->fetch())
		{
			$user->setDateBirth(new \DateTime($user->datebirth()));
			
			return $user;
		}
		
		return null;
	}
	protected function modify(User $user)
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
	
}