<?php
namespace Model;
use \OCFram\Manager;
use \Entity\User;
abstract class UserManager extends Manager
{
	const MMY_INVITE = 3;
	
	/**
	 * Méthode permettant d'ajouter un user.
	 * @param $user User Le user à ajouter
	 * @return void
	 */
	abstract protected function add(User $user);
	
	/**
	 * Méthode permettant d'enregistrer un user.
	 * @param $user User le user à enregistrer
	 * @see self::add()
	 * @see self::modify()
	 * @return void
	 */
	public function save(User $user)
	{
		if ($user->isValid())
		{
			$user->setPassword(password_hash($user->password(), PASSWORD_BCRYPT));
			//$user->isNew() ? $this->add($user) : $this->modify($user); si update prevu
			$this->add($user);
		}
		else
		{
			throw new \RuntimeException('Le user doit être validée pour être enregistrée');
		}
	}
	/**
	 * Méthode renvoyant le nombre de user total.
	 * @return int
	 */
	abstract public function count();
	/**
	 * Méthode permettant de supprimer un user.
	 * @param $id int L'identifiant du user à supprimer
	 * @return void
	 */
	abstract public function delete($id);
	/**
	 * Méthode retournant une liste de user demandée.
	 * @param $debut int Le premièr user à sélectionner
	 * @param $limite int Le nombre de user à sélectionner
	 * @return array La liste des users. Chaque entrée est une instance de User.
	 */
	abstract public function getList($debut = -1, $limite = -1);
	
	/**
	 * Méthode retournant un user précis.
	 * @param $id int L'identifiant du user à récupérer
	 * @return User Le user demandée
	 */
	abstract public function getUnique($id);
	/**
	 * Méthode permettant de modifier un user.
	 * @param $user User le user à modifier
	 * @return void
	 */
	abstract protected function modify(User $user);
	
	/**
	 * recuperer un user grace a son login
	 * @param string $login
	 * @return User
	 */
	abstract public function getUserUsingLogin($login);
}