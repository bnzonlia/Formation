<?php

namespace App\Backend\Modules\Connexion;

use Model\UserManager;
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\User;

class ConnexionController extends BackController {
	public function executeIndex( HTTPRequest $request ) {
		$this->page->addVar( 'title', 'Connexion' );
		
		if ( $request->postExists( 'login' ) ) {
			$login    = $request->postData( 'login' );
			$password = $request->postData( 'password' );
			
			$manager = $this->managers->getManagerOf( 'User' );
			$user    = $manager->getUserUsingLogin( $login );
			
			if ( $login == $user->login() && $password == $user->password() && $user->membertype() == 1 ) {
				$this->app->user()->setAuthenticated( true );
				$this->app->user()->setAttribute( 'Member', $user );
				$this->app->httpResponse()->redirect( '.' );
			}
			elseif ( $login == $user->login() && $password == $user->password() && $user->membertype() == 0 ) {
				$this->app->user()->setAuthenticated( true );
				$this->app->user()->setAttribute( 'Member', $user );
				$this->app->httpResponse()->redirect( '.' );
			}
			else {
				$this->app->user()->setFlash( 'login ou mot de passe incorrect' );
			}
		}
	}
	
	public function executeLogout( HTTPRequest $request ) {
		session_unset();
		session_destroy();
		$this->app->httpResponse()->redirect( '/' );
	}
}
