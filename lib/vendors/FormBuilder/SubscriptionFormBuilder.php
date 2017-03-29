<?php
namespace FormBuilder;

use \OCFram\FormBuilder;
use \OCFram\StringField;
use \OCFram\TextField;
use \OCFram\MaxLengthValidator;
use \OCFram\NotNullValidator;

class SubscriptionFormBuilder extends FormBuilder
{
	public function build()
	{
		$this->form->add(new StringField([
			'label' => 'login',
			'name' => 'login',
			'maxLength' => 20,
			'validators' => [
				new MaxLengthValidator('Le login spécifié est trop long (20 caractères maximum)', 20),
				new NotNullValidator('Merci de spécifier le login du user'),
			],
		]))
				   ->add(new StringField([
					   'label' => 'Password',
					   'name' => 'Password',
					   'maxLength' => 100,
					   'validators' => [
						   new MaxLengthValidator('Le password spécifié est trop long (100 caractères maximum)', 100),
						   new NotNullValidator('Merci de spécifier le password '),
					   ],
				   ]))
					->add(new StringField([
						'label' => 'Password Confirmation',
						'name' => 'PasswordConfirmation',
						'maxLength' => 100,
						'validators' => [
							new MaxLengthValidator('Le password spécifié pas valide (100 caractères maximum)', 100),
							new NotNullValidator('Merci de spécifier le bon password '),
						],
					]))
			->add(new StringField([
				'label' => 'Email',
				'name' => 'Email',
				'maxLength' => 100,
				'validators' => [
					new MaxLengthValidator('Email spécifié est trop long (100 caractères maximum)', 100),
					new NotNullValidator('Merci de spécifier Email '),
				],
			]))
			->add(new StringField([
				'label' => 'Email Confirmation',
				'name' => 'EmailConfirmation',
				'maxLength' => 100,
				'validators' => [
					new MaxLengthValidator('Email spécifié pas valide (100 caractères maximum)', 100),
					new NotNullValidator('Merci de spécifier le bon Email '),
				],
			]));
	}
}