<?php
namespace Entity;

use \OCFram\Entity;

class User  extends Entity
{
	protected $id,
		$firstname,
		$lastname,
		$login,
		$password,
		$datebirth,
		$membertype;

	const ID_INVALIDE = 0;
	const FIRSTNAME_INVALIDE = 1;
	const LASTNAME_INVALIDE = 2;
	const LOGIN_INVALIDE = 3;
	const PASSWORD_INVALIDE = 4;
	const MEMBERTYPE_INVALIDE = 5;
	
	public function isValid()
	{
		return !(empty($this->firstname) || empty($this->lastname) || empty($this->login) || empty($this->password));
	}
	
	
	// SETTERS //

	public function setId($id)
	{
		if (!is_int($id) || empty($id))
		{
			$this->erreurs[] = self::ID_INVALIDE;
		}

		$this->id = $id;
	}

	public function setFirstname($firstname)
	{
		if (!is_string($firstname) || empty($firstname))
		{
			$this->erreurs[] = self::FIRSTNAME_INVALIDE;
		}
		
		$this->firstname = $firstname;
	}
	
	public function setLastname($lastname)
	{
		if (!is_string($lastname) || empty($lastname))
		{
			$this->erreurs[] = self::LASTNAME_INVALIDE;
		}
		
		$this->lastname = $lastname;
	}
	
	public function setLogin($login)
	{
		if (!is_string($login) || empty($login))
		{
			$this->erreurs[] = self::LOGIN_INVALIDE;
		}
		
		$this->login = $login;
	}
	
	public function setPassword($password)
	{
		if (!is_string($password) || empty($password))
		{
			$this->erreurs[] = self::PASSWORD_INVALIDE;
		}
		
		$this->password = $password;
	}

	public function setDatebirth(\DateTime $datebirth)
	{
		$this->datebirth = $datebirth;
	}

	public function setMembertype($membertype)
	{
		if (!is_int($membertype) || empty($membertype))
		{
			$this->erreurs[] = self::MEMBERTYPE_INVALIDE;
		}

		$this->membertype = $membertype;
	}
	// GETTERS //

	public function id()
	{
		return $this->id;
	}

	public function firstname()
	{
		return $this->firstname;
	}
	
	public function lastname()
	{
		return $this->lastname;
	}
	
	public function login()
	{
		return $this->login;
	}
	
	public function password()
	{
		return $this->password;
	}
	
	public function datebirth()
	{
		return $this->datebirth;
	}
	
	public function membertype()
	{
		return $this->membertype;
	}

	
	
}