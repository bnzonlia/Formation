<?php
namespace Entity;

use \OCFram\Entity;

class Comment extends Entity
{
	protected $news,
		$auteur,
		$contenu,
		$datec,
		$dateu;
	
	const AUTEUR_INVALIDE = 1;
	const CONTENU_INVALIDE = 2;
	
	public function isValid()
	{
		return !empty( $this->contenu ) && is_string( $this->contenu );
	}
	
	public function setNews($news)
	{
		$this->news = (int) $news;
	}
	
	public function setAuteur($auteur)
	{
		if (!is_int($auteur) || empty($auteur))
		{
			$this->erreurs[] = self::AUTEUR_INVALIDE;
		}
		
		$this->auteur = $auteur;
	}
	
	public function setContenu($contenu)
	{
		if (!is_string($contenu) || empty($contenu))
		{
			$this->erreurs[] = self::CONTENU_INVALIDE;
		}
		
		$this->contenu = $contenu;
	}
	
	public function setDatec(\DateTime $datec)
	{
		$this->datec = $datec;
	}
	
	public function setDateu(\DateTime $dateu)
	{
		$this->dateu = $dateu;
	}
	
	public function news()
	{
		return $this->news;
	}
	
	public function auteur()
	{
		return $this->auteur;
	}
	
	public function contenu()
	{
		return $this->contenu;
	}
	
	public function datec()
	{
		return $this->datec;
	}
	public function dateu()
	{
		return $this->dateu;
	}
	
}