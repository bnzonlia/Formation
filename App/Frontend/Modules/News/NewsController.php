<?php
namespace App\Frontend\Modules\News;
use Entity\News;
use Entity\User;
use FormBuilder\CommentWithAuthorFormBuilder;
use \OCFram\BackController;
use OCFram\Form;
use \OCFram\HTTPRequest;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \OCFram\FormHandler;
use OCFram\Router;


class NewsController extends BackController {
	public function executeIndex( HTTPRequest $request ) {
		$nombreNews       = self::$app->config()->get( 'nombre_news' );
		$nombreCaracteres = self::$app->config()->get( 'nombre_caracteres' );


		// On ajoute une définition pour le titre.
		$this->page->addVar( 'title', 'Liste des ' . $nombreNews . ' dernières news' );
		
		// On récupère le manager des news.
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
		$news = $this->managers->getManagerOf( 'News' )->getNewscUsingNewscId( $request->getData( 'id' ) );

		if ( empty( $news ) ) {
			self::$app->httpResponse()->redirect404();
		}
		$this->page->addVar( 'title', $news->titre() );
		$this->page->addVar( 'news', $news );
		$this->page->addVar( 'comments', $this->managers->getManagerOf( 'Comments' )->getCommentcUsingNewscId( $news->id() ) );
	}

	public function executeInsertComment( HTTPRequest $request ) {
		if ( self::$app->user()->isAuthenticated() ) {
			/** je recupère les informations du membre connecté */
			$user =self::$app->user()->getAttribute( 'Member' );

			// Si le formulaire a été envoyé
			if ( $request->method() == 'POST' ) {
				$comment = new Comment( [
					'news'    => $request->getData( 'news' ),
					'auteur'  => (int) $user->id(),
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
				self::$app->user()->setFlash( 'Le commentaire a bien été ajouté, merci !' );
				self::$app->httpResponse()->redirect( 'news-' . $request->getData( 'news' ) . '.html' );
			}
			$this->page->addVar( 'comment', $comment );
			$this->page->addVar( 'form', $form->createView() );
			$this->page->addVar( 'title', 'Ajout d\'un commentaire' );
		}
		else { // pas connecté

			// Si le formulaire a été envoyé.
			if ( $request->method() == 'POST' ) {
				$user = $this->managers->getManagerOf( 'User' )->getUserUsingLogin( $request->postData( 'auteur' ) );

				if ( $user == null ) {
					$user = new User( [
						'login'      => $request->postData( 'auteur' ),
						'membertype' => 3,
					] );
					$this->managers->getManagerOf('User')->addInvite($user);
				}

				$comment = new Comment( [
					'news'    => $request->getData( 'news' ),
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

			if ( $form->isValid() ) {

				$this->managers->getManagerOf( 'Comments' )->save( $comment );
				self::$app->user()->setFlash( 'Le commentaire a bien été ajouté, merci !' );
				self::$app->httpResponse()->redirect( 'news-' . $request->getData( 'news' ) . '.html' );
			}

			$formBuilder->form()->entity()->setAuteur( $request->postData( 'auteur' ) );

			/** @var Form $form */
			$form = $formBuilder->form();

			$this->page->addVar( 'comment', $comment );
			$this->page->addVar( 'form', $form->createView() );
			$this->page->addVar( 'title', 'Ajout d\'un commentaire' );


		}
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
	 * @param News $news
	 *
	 * @return string
	 */
	static public function getLinkToBuildShow(News $news) {
		$id = $news->id();
		if (empty($id)) {
			throw new \RuntimeException('Impossible de creer le lien de la news : L\'id de la News n\'est pas renseigné !');
		}
		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'show', array('id' => (int)$news->id()) );
	}

	/**
	 * Renvoie le lien de la page de creation d'un commentaire pour une news
	 * @param News $news
	 *
	 * @return string
	 */
	static public function getLinkToPutInsertComment(News $news) {
		$id = $news->id();
		if (empty($id)) {
			throw new \RuntimeException('Impossible de creer le commentaire de la news : L\'id de la News n\'est pas renseigné !');
		}
		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'insertComment', array('id' => (int)$news->id()) );
	}

	/**
	 * Renvoie le lien de la page d'inscription Frontend
	 *
	 * @return string
	 */
	static public function getLinkToBuildInscription() {
		return Router::getUrlFromModuleAndAction( 'Frontend', 'Inscription', 'inscription' );
	}
}