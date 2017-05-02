<?php
/**
 * Created by PhpStorm.
 * User: bnzonlia
 * Date: 28/04/2017
 * Time: 14:54
 */

namespace App\Traits;

use App\Frontend\Modules\Connexion\ConnexionController;
use App\Frontend\Modules\News\NewsController;
use Entity\User;
use OCFram\ApplicationComponent;
use OCFram\BackController;
use OCFram\HTTPResponse;
use Helpers\MenuHelper;

/**
 * trait MenuController
 *
 * Trait qui génère le menu et le contenu de la page en fonction de l'authentification du user.
 *
 * @package App\Traits
 */

 trait MenuController {
 	use MenuHelper;
	
	 /**
	  * Génère le menu d'une page HTML.
	  */
	 private function runHTML() {
		 /**
		  * @var $this BackController
		  */
		 $menu_a = [];
		 MenuHelper::addLink( $menu_a, NewsController::getLinkToBuildIndex(), 'Accueil' );
		 if ( ApplicationComponent::app()->user()->isAuthenticated() ) {
			 
			 MenuHelper::addLink( $menu_a, \App\Backend\Modules\News\NewsController::getLinkToBuildIndex(), 'Admin' );
				 
			 MenuHelper::addLink( MenuHelper::addLink( $menu_a, \App\Backend\Modules\Connexion\ConnexionController:: getLinkToClearConnection(), 'Se deconnecter' ),
				 \App\Backend\Modules\News\NewsController::getLinkToPutInsertNews(), 'Ajouter news' );
		 }
		 
		 else {
		 	
			 MenuHelper::addLink( MenuHelper::addLink( $menu_a, NewsController::getLinkToBuildConnexion(), 'Connexion' ), NewsController::getLinkToBuildInscription(), 'S\'inscrire' );
		 }
		 $this->page()->addVar( 'menu_a', $menu_a );
		
		 // Générer le flash
		 if ( ApplicationComponent::app()->user()->hasFlash() ) {
			 $flash = ApplicationComponent::app()->user()->getFlash();
			 $this->page()->addVar( 'flash', $flash );
		 }
		
		 // Générer les liens sur la page hors du menu
		 $h1_link_a                          = [];
		 MenuHelper::addLink($h1_link_a, NewsController::getLinkToBuildIndex(), 'Mon super site');
		 $this->page()->addVar( 'h1_link_a', $h1_link_a );
	 }
	
	 /**
	  * Génère le menu d'une page JSON
	  */
	 private function runJSON() {
	 }
	
	 /**
	  * Génère le menu de la page courante. Doit être appelée au début de chaque contrôleur.
	  */
	 public function run() {
		 /**
		  * @var $this BackController
		  */
		
		 switch ( $this->page()->format() ) {
			 case 'html':
				 $this->runHTML();
				 break;
			 case 'json':
				 $this->runJSON();
				 break;
			 default:
				 throw new \Exception( 'Format ' . $this->page()->format() . ' has no run method defined.' );
		 }
	 }
 }
