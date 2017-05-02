<?php
namespace App\Frontend\Modules\News;
use Entity\News;
use Entity\User;
use FormBuilder\CommentWithAuthorFormBuilder;
use Model\CommentsManager;
use Model\NewsManager;
use \OCFram\BackController;
use OCFram\Entity;
use OCFram\Form;
use \OCFram\HTTPRequest;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \OCFram\FormHandler;
use OCFram\Router;
use App\Traits\MenuController;


class NewsController extends BackController {
	use MenuController;
	
	public function executeIndex( HTTPRequest $request ) {
		$this->run();
		
		$nombreNews       = self::$app->config()->get( 'nombre_news' );
		$nombreCaracteres = self::$app->config()->get( 'nombre_caracteres' );


		// On ajoute une définition pour le titre.
		$this->page->addVar( 'title', 'Liste des ' . $nombreNews . ' dernières news' );
		
		// On récupère le manager des news.
		/** @var NewsManager $manager */
		$manager = $this->managers->getManagerOf( 'News' );
		
		$listeNews = $manager->getNewscAndUserSortByIdDesc( 0, $nombreNews );
		
		foreach ( $listeNews as $News ) {
			if ( strlen( $News->contenu() ) > $nombreCaracteres ) {
				$debut = substr( $News->contenu(), 0, $nombreCaracteres );
				$debut = substr( $debut, 0, strrpos( $debut, ' ' ) ) . '...';
				
				$News->setContenu( $debut );
			}
		}
		$this->page->addVar( 'listeNews', $listeNews );
	}
	
	public function executeShow( HTTPRequest $request ) {
		$this->run();
		$news = $this->managers->getManagerOf( 'News' )->getNewscUsingNewscId( $request->getData( 'id' ) );
		
		if ( empty( $news ) ) {
			self::$app->httpResponse()->redirect404();
		}
		$this->page->addVar( 'title', $news->titre() );
		$this->page->addVar( 'news', $news );
		$comments =$this->managers->getManagerOf( 'Comments' )->getCommentcUsingNewscId( $news->id() );
		//$this->page->addVar( 'comments', $comments );

		// envoyer le dernier commentaire de la liste
		$Comment_last = new Comment( [ 'id' => 0 ] );

		foreach ( $comments as $Comment_temp ) {
			if ( $Comment_temp->id() > $Comment_last->id() ) {
				$Comment_last = $Comment_temp;
			}
		}
		$news->link_insert_comment_json   = self::getLinkToPutInsertCommentFromAjax( $news );
		$news->link_refresh_comments_json = self::getLinkToBuildRefreshCommentsFromAjax($news,$Comment_last);
		
		// création du form
		$formBuilder = new CommentWithAuthorFormBuilder( new Comment(), $this );
		$formBuilder->build();

		$form = $formBuilder->form();

		$this->page->addVar( 'title_form', 'Ajout d\'un commentaire' );
		$this->page->addVar( 'form', $form->createView() );
		$this->page->addVar( 'submit', 'Commenter' );
		$this->page->addVar( 'form_action', self::getLinkToPutInsertCommentFromAjax( $news ) );
		$this->page->addVar( 'lastcomment', $Comment_last->id() );
		// Ajouter la date et heure
		$this->page->addVar( 'dateupdate', ( new \DateTime() )->format( 'Y-m-d H:i:s.u' ) );
		
	}

	public function executeInsertComment( HTTPRequest $request ) {
		$this->run();
		/** je recupère les informations du membre connecté */
		$user = self::$app->user()->getAttribute( 'Member' );

		// Si le formulaire a été envoyé
		if ( $request->method() == 'POST' ) {
			$comment = new Comment( [
				'news'    => $request->getData( 'id' ),
				'auteur'  => (int)$user->id(),
				'contenu' => $request->postData( 'contenu' ),
			] );
		}
		else {
			$comment = new Comment;
		}

		$formBuilder = new CommentFormBuilder( $comment );
		$formBuilder->build();

		$form = $formBuilder->form();

		if ( $request->method() == 'POST' && $form->isValid() ) {
			$this->managers->getManagerOf( 'Comments' )->save( $comment );
			/** @var News $newsManager */
			$news = $this->managers->getManagerOf( 'News' )->getNewscUsingNewscId( $request->getData( 'id' ) );
			self::$app->user()->setFlash( 'Le commentaire a bien été ajouté, merci !' );
			self::$app->httpResponse()->redirect( NewsController::getLinkToBuildShow( $news ) );
		}
		$this->page->addVar( 'comment', $comment );
		$this->page->addVar( 'form', $form->createView() );
		$this->page->addVar( 'title', 'Ajout d\'un commentaire' );
	}

	/**
	 * Methode pour gerer l'insertion d'un commentaire depuis une requête Ajax
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeInsertCommentFromAjax( HTTPRequest $request ) {
		$this->run();
		if ( self::$app->user()->isAuthenticated() ) {
			/** je recupère les informations du membre connecté */
			$user = self::$app->user()->getAttribute( 'Member' );

			// Si le formulaire a été envoyé
			if ( $request->method() == 'POST' ) {
				$comment = new Comment( [
					'news'    => $request->getData( 'id' ),
					'auteur'  => (int)$user->id(),
					'contenu' => $request->postData( 'contenu' ),
				] );
			}
			else {
				$comment = new Comment;
			}

			$formBuilder = new CommentFormBuilder( $comment );
			$formBuilder->build();

			$form = $formBuilder->form();

			if ( $request->method() == 'POST' && $form->isValid() ) {
				$this->managers->getManagerOf( 'Comments' )->save( $comment );
				$news = $this->managers->getManagerOf( 'News' )->getNewscUsingNewscId( $request->getData( 'id' ) );
				self::$app->httpResponse()->redirect( NewsController::getLinkToPutInsertCommentFromAjax( $news ) );
			}
			else {
				// Sinon on envoie les erreurs
				$error_a = [ ];
				foreach ( $form->Field_a() as $Field ) {

					if ( $Field->errorMessage() != null ) {
						$error_a[ $Field->name() ] = $Field->errorMessage();
					}
				}
				$this->page->addVar( 'error_a', $error_a );
			}
		}
		// pas connecte : guest
		else {
			// Si le formulaire a été envoyé.
			if ( $request->method() == 'POST' ) {
				$user = $this->managers->getManagerOf( 'User' )->getUserUsingLogin( $request->postData( 'auteur' ) );

				if ( $user == null ) {
					$user = new User( [
						'login'      => $request->postData( 'auteur' ),
						'membertype' => 3,
					] );
					//$this->managers->getManagerOf( 'User' )->addInvite( $user );
				}

				$comment = new Comment( [
					'news'    => $request->getData( 'id' ),
					'auteur'  => $request->postData( 'auteur' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
			}
			else {
				$comment = new Comment;
			}

			$formBuilder = new CommentWithAuthorFormBuilder( $comment, $this );
			$formBuilder->build();

			/** @var Form $form */
			$form = $formBuilder->form();

			if ( $request->method() == 'POST' && $form->isValid() ) {

				$this->managers->getManagerOf( 'Comments' )->save( $comment );
				/** @var News $newsManager */
				//self::$app->user()->setFlash( 'Le commentaire a bien été ajouté, merci !' );
				$news = $this->managers->getManagerOf( 'News' )->getNewscUsingNewscId( $request->getData( 'id' ) );
				self::$app->httpResponse()->redirect( NewsController::getLinkToPutInsertCommentFromAjax( $news ) );
			}

			else {
				// Sinon on envoie les erreurs
				$error_a = [ ];
				foreach ( $form->Field_a() as $Field ) {

					if ( $Field->errorMessage() != null ) {
						$error_a[ $Field->name() ] = $Field->errorMessage();
					}
				}
				$this->page->addVar( 'error_a', $error_a );
			}
		}
	}

	public function executeRefreshCommentsFromAjax( HTTPRequest $Request ) {
		$this->run();
		/** @var CommentsManager $Comments_manager */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		// On vérifie l'existence de la news
		$News_manager = $this->managers->getManagerOf( 'News' );
		if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
			die( 'Impossible de rafraîchir les commentaires : la news n\'existe plus !' );
		}

		// id du dernier commentaire renseigné
		$last_comment_id=$Request->postData('lastcomment');
		$last_news_id=$Request->getData('id');

		// Sélection des nouveaux commentaires
		/** @var Comment [] $New_comment_a */
		$New_comment_a = $Comments_manager->getCommentcUsingNewscIdFilterOverDatecreationSortByIdDesc( $last_news_id, $last_comment_id);
		

		$this->page->addVar( 'New_comment_a', $New_comment_a );
		
		// Sélection des commentaires modifiés
		$Update_comment_a = $Comments_manager->getCommentcAndUsercUsingNewscIdFilterOverEditedAfterDateupdateAndCreatedBeforeDateupdateSortByIdDesc( $Request->getData( 'id' ), $Request->postData( 'dateupdate' ) );
		//var_dump($Request->postdata('dateupdate'));
		$this->page->addVar( 'Update_comment_a', $Update_comment_a );
		

		// Sélection des ids supprimés
		if ( $Request->postExists( 'comments_ids_a' ) ) {
			$delete_ids_a = $Comments_manager->filterCommentcUsingUnexistantCommentcId( explode( ',', $Request->postData( 'comments_ids_a' ) ) );
		}
		else {
			$delete_ids_a = [];
		}
		$this->page->addVar( 'delete_ids_a', $delete_ids_a );

		// Générer la date du refresh
		$this->page->addVar( 'dateupdate', ( new \DateTime() )->format( 'Y-m-d H:i:s.u' ) );
	}

	/**
	 * Renvoie le lien de la page d'accueil Frontend
	 *
	 * @return string
	 */
	static public function getLinkToBuildIndex() {
		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'index' );
	}

	/**
	 * Renvoie le lien de la page complete de la news
	 *
	 * @param News $news
	 *
	 * @return string
	 */
	static public function getLinkToBuildShow( News $news ) {
		$id = $news->id();
		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Impossible de creer le lien de la news : L\'id de la News n\'est pas renseigné !' );
		}

		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'show', array( 'id' => (int)$news->id() ) );
	}

	/**
	 * Renvoie le lien de la page de creation d'un commentaire pour une news
	 *
	 * @param News $news
	 *
	 * @return string
	 */
	static public function getLinkToPutInsertComment( News $news ) {

		$id = $news->id();

		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Impossible de creer le commentaire de la news : L\'id de la News n\'est pas renseigné !' );
		}

		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'insertComment', array( 'id' => (int)$news->id() ) );
	}
	
	static public function getLinkToPutInsertCommentFromAjax( News $News ) {
		$id = $News->id();
		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Can\'t create News link : News id is unknown !' );
		}
		
		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'insertCommentFromAjax', array( 'id' => (int)$id ) );
	}

	/**
	 * Renvoie le lien de la page d'inscription Frontend
	 *
	 * @return string
	 */
	static public function getLinkToBuildInscription() {
		return Router::getUrlFromModuleAndAction( 'Frontend', 'Inscription', 'inscription' );
	}
	
	static public function getLinkToBuildConnexion() {
		return Router::getUrlFromModuleAndAction( 'Frontend', 'Connexion', 'index' );
	}
	
	static public function getLinkToBuildRefreshCommentsFromAjax( News $news , Comment $comments ) {
		$id = $news->id();
		$comment_id=$comments->id();
		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Can\'t create News link : News id is unknown !' );
		}

		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'refreshCommentsFromAjax', array(
			'id' => (int)$id , 
			'lastcomment' => (int) $comment_id) );
	}
}
