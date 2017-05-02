<?php

namespace App\Frontend\Modules\Connexion;

use App\Backend\Modules\News\NewsController;
use App\Traits\MenuController;
use Model\UserManager;
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\User;

class ConnexionController extends BackController {
	use MenuController;
	
	public function executeIndex( HTTPRequest $request ) {
		$this->run();
		$this->page->addVar( 'title', 'Connexion' );

		if ( $request->postExists( 'login' ) ) {
			$login    = $request->postData( 'login' );
			$password = $request->postData( 'password' );
/** @var UserManager $manager */
			$manager = $this->managers->getManagerOf( 'User' );
			$user    = $manager->getUserUsingLogin( $login );

			// si le use n'est pas invité
			if ( $login == $user->login() && password_verify ( $password , $user->password()) && $user->membertype() != 3 ) {
				self::$app->user()->setAuthenticated( true );
				self::$app->user()->setAttribute( 'Member', $user );

				if($user->membertype() == 0){
					// redirection admin
					self::$app->httpResponse()->redirect( NewsController::getLinkToBuildIndex() );
				}
				self::$app->httpResponse()->redirect( NewsController::getLinkToBuildIndex() );

			}else {
				self::$app->user()->setFlash( 'login ou mot de passe incorrect' );
			}
		}
	}

	public function executeLogout( HTTPRequest $request ) {
		$this->run();
		session_unset();
		session_destroy();
		self::$app->httpResponse()->redirect(\App\Frontend\Modules\News\NewsController::getLinkToBuildIndex() );
	}
	
}
