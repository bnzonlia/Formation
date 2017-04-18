<?php

namespace App\Frontend\Modules\Inscription;

use App\Backend\Modules\Connexion\ConnexionController;
use App\Backend\Modules\News\NewsController;
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\User;
use \FormBuilder\SubscriptionFormBuilder;
use \OCFram\FormHandler;

class InscriptionController extends BackController {
	public function executeInscription( HTTPRequest $request ) {
		if ( $request->method() == 'POST' ) {
			$user = new User( [
				'firstname'    => $request->postData( 'firstname' ),
				'lastname' => $request->postData( 'lastname' ),
				'login'    => $request->postData( 'login' ),
				'password' => $request->postData( 'password' ),
				'email'    => $request->postData( 'email' ),
			] );
		}
		else {
			$user = new User;
		}
		
		$formBuilder = new SubscriptionFormBuilder( $user, $this );
		$formBuilder->build();
		
		$form        = $formBuilder->form();
		$formHandler = new FormHandler( $form, $this->managers->getManagerOf( 'User' ), $request );
		
		if ( $formHandler->process() ) {
			self::$app->user()->setFlash( 'Le user a bien été ajouté !' );
			self::$app->httpResponse()->redirect( NewsController::getLinkToBuildIndex() );
		}
		
		$this->page->addVar( 'form', $form->createView() );
		$this->page->addVar( 'title', 'Inscription' );
	}
}