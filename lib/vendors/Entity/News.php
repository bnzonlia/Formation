<?php
namespace Entity;

use \OCFram\Entity;


class News extends Entity
{
	protected $auteur,
		$titre,
		$contenu,
		$dateAjout,
		$dateModif,
	    $user;
	
	const AUTEUR_INVALIDE = 1;
	const TITRE_INVALIDE = 2;
	const CONTENU_INVALIDE = 3;
	const LOGIN_INVALIDE=4;
	
	public function isValid()
	{
		return !( empty($this->titre) || empty($this->contenu));
	}
	
	
	// SETTERS //

	public function setUser( User $user ) {
		if ($user->isValid()) {
			$this->user = $user;
		}
	}
	
	public function setAuteur($auteur)
	{
		if (!is_int($auteur) || empty($auteur))
		{
			$this->erreurs[] = self::AUTEUR_INVALIDE;
		}
		
		$this->auteur = $auteur;
	}
	public function setLogin($login)
	{
		if (!is_string($login) || empty($login))
		{
			$this->erreurs[] = self::AUTEUR_INVALIDE;
		}

		$this->login = $login;
	}
	
	public function setTitre($titre)
	{
		if (!is_string($titre) || empty($titre))
		{
			$this->erreurs[] = self::TITRE_INVALIDE;
		}
		
		$this->titre = $titre;
	}
	
	public function setContenu($contenu)
	{
		if (!is_string($contenu) || empty($contenu))
		{
			$this->erreurs[] = self::CONTENU_INVALIDE;
		}
		
		$this->contenu = $contenu;
	}
	
	public function setDateAjout(\DateTime $dateAjout)
	{
		$this->dateAjout = $dateAjout;
	}
	
	public function setDateModif(\DateTime $dateModif)
	{
		$this->dateModif = $dateModif;
	}
	
	// GETTERS //

	public function user()
	{
		return $this->user;
	}

	public function auteur()
	{
		return $this->auteur;
	}
	public function login()
	{
		return $this->login;
	}
	
	public function titre()
	{
		return $this->titre;
	}
	
	public function contenu()
	{
		return $this->contenu;
	}
	
	public function dateAjout()
	{
		return $this->dateAjout;
	}
	
	public function dateModif()
	{
		return $this->dateModif;
	}
}