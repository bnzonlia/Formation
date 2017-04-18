<?php

namespace FormBuilder;

namespace FormBuilder;
use OCFram\BackController;
use OCFram\Entity;
use OCFram\Managers;
use \OCFram\FormBuilder;
use OCFram\NotExistValidator;
use \OCFram\StringField;
use \OCFram\TextField;
use \OCFram\MaxLengthValidator;
use \OCFram\NotNullValidator;

class CommentWithAuthorFormBuilder extends FormBuilder
{
	protected $Controller;

	public function __construct( Entity $Entity, BackController $Controller) {
		parent::__construct( $Entity );

		$this->setController($Controller);
	}

	public function build()
	{
		if(!$this->Controller->app()->user()->isAuthenticated())
		{
			$invite = new StringField([
				'label' => 'Auteur',
				'name' => 'auteur',
				'maxLength' => 50,
				'validators' => [
					new MaxLengthValidator('L\'auteur spécifié est trop long (50 caractères maximum)', 50),
					new NotExistValidator('Merci d\'utiliser un nom différent d\'un utilisateur existant',$this->Controller->managers()->getManagerOf('User'),' getUserWithoutGuestUsingLogin'),
					new NotNullValidator('Merci de spécifier l\'auteur du commentaire'),

				],
			]);
			$this->form->add($invite);
		}


		$this->form->add(new TextField([
			'label' => 'Contenu',
			'name' => 'contenu',
			'rows' => 7,
			'cols' => 50,
			'validators' => [
				new NotNullValidator('Merci de spécifier votre commentaire'),
			],
		]));
	}

	/**
	 * @param BackController $Controller $Controller
	 */
	public function setController(BackController  $Controller) {
		$this->Controller = $Controller;
	}
}