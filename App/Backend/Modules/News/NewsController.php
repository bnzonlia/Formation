<?php

namespace App\Backend\Modules\News;

use App\Traits\MenuController;
use Model\CommentsManager;
use Model\NewsManagerPDO;
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\News;
use \Entity\User;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \FormBuilder\NewsFormBuilder;
use \OCFram\FormHandler;
use OCFram\Manager;
use OCFram\Router;

class NewsController extends BackController {
	use MenuController;
	public function executeDelete( HTTPRequest $request ) {
		$this->run();
		$newsId = $request->getData( 'id' );
		
		/** je recupère les informations du membre connecté */
		/** @var User $user */
		$user = self::$app->user()->getAttribute( 'Member' );
		/** @var NewsManagerPDO $manager */
		$manager=$this->managers->getManagerOf( 'News' );
		$news= $manager->getNewscUsingNewscId($newsId);

		/** si le user est un admin supreme il supprime */
		if ($user->membertype()== 1 ) {
			$this->managers->getManagerOf( 'News' )->deleteNewscUsingNewscId( $newsId );
			$this->managers->getManagerOf( 'Comments' )->deleteCommentcUsingNewscId( $newsId );
			self::$app->user()->setFlash( 'La news a bien été supprimée !' );
			self::$app->httpResponse()->redirect( NewsController::getLinkToBuildIndex() );
		}
		/** sinon , c'est un admin partiel et il ne peut que supprimer sa news */
		 elseif ($user->membertype()== 0 && $user->id() == $news->auteur())
		 {
			 $this->managers->getManagerOf( 'News' )->deleteNewscUsingNewscId( $newsId );
			 $this->managers->getManagerOf( 'Comments' )->deleteCommentcUsingNewscId( $newsId );
			 self::$app->user()->setFlash( 'La news a bien été supprimée !' );
			 self::$app->httpResponse()->redirect( NewsController::getLinkToBuildIndex() );
		 }
		else {
			self::$app->user()->setFlash( 'vous avez pas le droit de supprimer!' );
			self::$app->httpResponse()->redirect(NewsController::getLinkToBuildIndex());
		}
	}
	
	public function executeDeleteComment( HTTPRequest $request ) {
		$this->run();
		/** je recupère les informations du membre connecté */
		$user = self::$app->user()->getAttribute( 'Member' );
		$manager=$this->managers->getManagerOf( 'Comments' );
		$com= $manager->getCommentcUsingCommentcId($request->getData( 'id' ));
		/** si le user est un admin supreme il supprime */
		if ($user->membertype()== 1 ) {

			$this->managers->getManagerOf( 'Comments' )->deleteCommentcUsingCommentcId( $request->getData( 'id' ) );
			self::$app->user()->setFlash( 'Le commentaire a bien été supprimé !' );
			self::$app->httpResponse()->redirect( NewsController::getLinkToBuildIndex() );
		}
		/** sinon , c'est un admin partiel et il ne peut que supprimer son commentaire */
		elseif ($user->membertype()== 0 && (int) $user->id() == $com->auteur())
		{
			$this->managers->getManagerOf( 'Comments' )->deleteCommentcUsingCommentcId( $request->getData( 'id' ) );
			self::$app->user()->setFlash( 'Le commentaire a bien été supprimé !' );
			self::$app->httpResponse()->redirect(NewsController::getLinkToBuildIndex() );
		}
		else {
			self::$app->user()->setFlash( 'vous avez pas le droit de supprimer!' );
			self::$app->httpResponse()->redirect(NewsController::getLinkToBuildIndex());
		}

	}
	
	public function executeDeleteCommentFromAjax( HTTPRequest $request ) {
		$this->run();
		/** je recupère les informations du membre connecté */
		/** @var User $user */
		$user = self::$app->user()->getAttribute( 'Member' );
		/** @var CommentsManager $manager */
		$manager=$this->managers->getManagerOf( 'Comments' );
		/** @var Comment $com */
		$com= $manager->getCommentcUsingCommentcId($request->getData( 'id' ));
		/** si le user est un admin supreme il supprime */
		if ($user->membertype()== 1 ) {
			
			$this->managers->getManagerOf( 'Comments' )->deleteCommentcUsingCommentcId( $request->getData( 'id' ) );
		}
		/** sinon , c'est un admin partiel et il ne peut que supprimer son commentaire */
		elseif ($user->membertype()== 0 && (int) $user->id() == $com->auteur())
		{
			$this->managers->getManagerOf( 'Comments' )->deleteCommentcUsingCommentcId( $request->getData( 'id' ) );
		}
	}
	
	/**
	 * Met à jour un commentaire.
	 *
	 * Si le commentaire n'existe pas, redirige vers une erreur 404.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeUpdateCommentFromAjax( HTTPRequest $Request ) {
		$this->run();
		/** je recupère les informations du membre connecté */
		/** @var User $user */
		$user = self::$app->user()->getAttribute( 'Member' );
		/** @var CommentsManager $manager */
		$manager=$this->managers->getManagerOf( 'Comments' );
		// Récupérer le commentaire
		$Comment = $manager->getCommentcUsingCommentcId( $Request->getData( 'id' ) );
		/** si le user est un admin supreme il supprime */
		if ($user->membertype()== 1 ) {
			/** @var CommentsManager $manager */
			
				// Construire le formulaire
				$Form_builder = new CommentFormBuilder( $Comment );
				$Form_builder->build();
				$Form = $Form_builder->form();
				$this->page->addVar( 'Comment', $Comment );
			}
		
		elseif ($user->membertype()== 0 && (int) $user->id() == $Comment->auteur()) {
			// Construire le formulaire
			$Form_builder = new CommentFormBuilder( $Comment );
			$Form_builder->build();
			$Form = $Form_builder->form();
			$this->page->addVar( 'form', $Form->createView() );
			$this->page->addVar( 'Comment', $Comment );
		}
		}
	
	public function executeAfterUpdateForSaveCommentFromAjax( HTTPRequest $Request ) {
		$this->run();
		/** je recupère les informations du membre connecté */
		/** @var User $user */
		$user = self::$app->user()->getAttribute( 'Member' );
		/** @var CommentsManager $manager */
		$manager=$this->managers->getManagerOf( 'Comments' );
		
		// Récupérer le contenu du nouveau commentaire
		$Comment = $manager->getCommentcUsingCommentcId( $Request->getData( 'id' ) );
		
		 
		/** si le user est un admin supreme il sauvegarde */
		if ($user->membertype()== 1 ) {
			if ( $Request->postData( 'contenu' ) !== null ) {
				$Comment->setContenu( $Request->postData( 'contenu' ) );
			}
			
			if ( null === $Comment ) {
				die( 'Le commentaire à éditer n\'existe pas !');
			}
			
			else {
				// Construire le formulaire
				$Form_builder = new CommentFormBuilder( $Comment );
				$Form_builder->build();
				$Form = $Form_builder->form();
				
				if ( $Request->postData( 'contenu' ) !== null ) {
					// Sauvegarder avec le FormHandler
					$Form_handler = new FormHandler( $Form, $manager, $Request );
					if ( !$Form_handler->process() ) {
						$error_a = [ ];
						// On envoie les erreurs si besoin
						foreach ( $Form->Field_a() as $Field ) {
							if ( $Field->errorMessage() != null ) {
								$error_a[ $Field->name() ] = $Field->errorMessage();
							}
						}
						// Envoyer les erreurs
						$this->page->addVar( 'form', $Form->createView() );
					}
					// Récupérer le nouvel état du commentaire (date d'update par exemple)
					else {
						$Comment = $manager->getCommentcUsingCommentcId( $Request->getData( 'id' ) );
					}
				}
				else {
					$this->page->addVar( 'form', $Form->createView() );
				}
				// Ne pas envoyer le form si OK
				$this->page->addVar( 'Comment', $Comment );
			}
		}
		elseif ($user->membertype()== 0 && (int) $user->id() == (int) $Comment->auteur()) {
			//var_dump($Request->postData( 'contenu' ));
				if ( $Request->postData( 'contenu' ) !== null ) {
					$Comment->setContenu( $Request->postData( 'contenu' ) );
				}
				
				if ( null === $Comment ) {
					die( 'Le commentaire à éditer n\'existe pas !');
				}
			
				else {
					// Construire le formulaire
					$Form_builder = new CommentFormBuilder( $Comment );
					$Form_builder->build();
					$Form = $Form_builder->form();
					
					if ( $Request->postData( 'contenu' ) !== null ) {
						// Sauvegarder avec le FormHandler
						$Form_handler = new FormHandler( $Form, $manager, $Request );
						if ( !$Form_handler->process() ) {
							$error_a = [ ];
							// On envoie les erreurs si besoin
							foreach ( $Form->Field_a() as $Field ) {
								if ( $Field->errorMessage() != null ) {
									$error_a[ $Field->name() ] = $Field->errorMessage();
								}
							}
							// Envoyer les erreurs
							$this->page->addVar( 'form', $Form->createView() );
						}
						// Récupérer le nouvel état du commentaire (date d'update par exemple)
						else {
							$Comment = $manager->getCommentcUsingCommentcId( $Request->getData( 'id' ) );
						}
					}
					else {
						$this->page->addVar( 'form', $Form->createView() );
					}
					// Ne pas envoyer le form si OK
					$this->page->addVar( 'Comment', $Comment );
				}
			}
		}
	
	public function executeIndex( HTTPRequest $request ) {
		$this->run();
		$this->page->addVar( 'title', 'Gestion des news' );
		
		/** @var NewsManagerPDO $manager */
		$manager = $this->managers->getManagerOf( 'News' );
		
		$this->page->addVar( 'listeNews', $manager->getNewscAndUserSortByIdDesc() );
		$this->page->addVar( 'nombreNews', $manager->countNewsc() );
	}
	
	public function executeInsert( HTTPRequest $request ) {
		$this->run();
		$this->processForm( $request );
		
		$this->page->addVar( 'title', 'Ajout d\'une news' );
	}
	
	public function executeUpdate( HTTPRequest $request ) {
		$this->run();
		$this->processForm( $request );
		
		$this->page->addVar( 'title', 'Modification d\'une news' );
	}
	
	public function executeUpdateComment( HTTPRequest $request ) {
		$this->run();
		$this->page->addVar( 'title', 'Modification d\'un commentaire' );

		/** je recupère les informations du membre connecté */
		/** @var User $user */
		$user = self::$app->user()->getAttribute( 'Member' );
		$manager=$this->managers->getManagerOf( 'Comments' );
		$com= $manager->getCommentcUsingCommentcId($request->getData( 'id' ));

		/** si le user est un admin supreme il modifie tous Les commentaires */
		if ($user->membertype()== 1 ) {

			if ( $request->method() == 'POST' ) {
				$comment = new Comment( [
					'id'      => $request->getData( 'id' ),
					'auteur' => $request->postData('auteur'),
					'contenu' => $request->postData( 'contenu' ),
				] );
			}
			else {
				$comment = $this->managers->getManagerOf( 'Comments' )->getCommentcUsingCommentcId( $request->getData( 'id' ) );
			}

			$formBuilder = new CommentFormBuilder( $comment );
			$formBuilder->build();

			$form = $formBuilder->form();

			$formHandler = new FormHandler( $form, $this->managers->getManagerOf( 'Comments' ), $request );

			if ( $formHandler->process() ) {
				self::$app->user()->setFlash( 'Le commentaire a bien été modifié' );

				self::$app->httpResponse()->redirect( NewsController::getLinkToBuildIndex() );
			}

			$this->page->addVar( 'form', $form->createView() );
		}
		/** sinon , c'est un admin partiel et il ne peut que supprimer son commentaire */
		elseif ($user->membertype()== 0 && (int) $user->id() == $com->auteur())
		{

			if ( $request->method() == 'POST' ) {
				$comment = new Comment( [
					'id'      => $request->getData( 'id' ),
					'auteur' => (int) $user->id(),
					'contenu' => $request->postData( 'contenu' ),
				] );
			}
			else {
				$comment = $this->managers->getManagerOf( 'Comments' )->getCommentcUsingCommentcId( $request->getData( 'id' ) );
			}

			$formBuilder = new CommentFormBuilder( $comment );
			$formBuilder->build();

			$form = $formBuilder->form();

			$formHandler = new FormHandler( $form, $this->managers->getManagerOf( 'Comments' ), $request );

			if ( $formHandler->process() ) {
				self::$app->user()->setFlash( 'Le commentaire a bien été modifié' );

				self::$app->httpResponse()->redirect( NewsController::getLinkToBuildIndex() );
			}

			$this->page->addVar( 'form', $form->createView() );
		}
		else {
			self::$app->user()->setFlash( 'vous avez pas le droit de modifier!' );
			self::$app->httpResponse()->redirect( NewsController::getLinkToBuildIndex() );
		}

	}
	
	public function processForm( HTTPRequest $request ) {
		
		$this->run();
		/** je recupère les informations du membre connecté */
		/** @var User $user */
		$user    = self::$app->user()->getAttribute( 'Member' );
		/** @var NewsManagerPDO $manager */
		$manager = $this->managers->getManagerOf( 'News' );
		$com = $manager->getNewscUsingNewscId( $request->getData( 'id' ) );
		 if($user->membertype()==0)
		 {
			 if ($request->method() == 'POST')
			 {
				 $news = new News([
					 'auteur' => $user->id(),
					 'titre' => $request->postData('titre'),
					 'contenu' => $request->postData('contenu')
				 ]);
				 if ($request->getExists('id'))
				 {
					 $news->setId($request->getData('id'));
				 }
			 }
			 else
			 {
				 // L'identifiant de la news est transmis si on veut la modifier
				 if ($request->getExists('id')&& $user->id()!=$com->auteur())
				 {
					 self::$app->user()->setFlash('pas encore de news a mon nom, impossible de modifier');
					 self::$app->httpResponse()->redirect(NewsController::getLinkToBuildIndex());
				 }

				else if ($request->getExists('id')&& $user->id()==$com->auteur())
				 {
					 $news = $this->managers->getManagerOf('News')->getNewscUsingNewscId($request->getData('id'));
				 }
				 else
				 {
					 $news = new News;

				 }

			 }
			 $formBuilder = new NewsFormBuilder($news);
			 $formBuilder->build();
			 $form = $formBuilder->form();
			 $formHandler = new FormHandler($form, $this->managers->getManagerOf('News'), $request);

			 if ($formHandler->process())
			 {
				 self::$app->user()->setFlash($news->isNew() ? 'La news a bien été ajoutée !' : 'La news a bien été modifiée !');

				 self::$app->httpResponse()->redirect(NewsController::getLinkToBuildIndex());
			 }
			 $this->page->addVar('form', $form->createView());

		 }

		else
		{

			if ($request->method() == 'POST')
			{
				$news = new News([
					'auteur' => $user->id(),
					'titre' => $request->postData('titre'),
					'contenu' => $request->postData('contenu')
				]);
				if ($request->getExists('id'))
				{
					$news->setId($request->getData('id'));
				}
			}
			else
			{
				// L'identifiant de la news est transmis si on veut la modifier
				if ($request->getExists('id'))
				{
					$news = $this->managers->getManagerOf('News')->getNewscUsingNewscId($request->getData('id'));
				}
				else
				{
					$news = new News;
				}
			}
			$formBuilder = new NewsFormBuilder($news);
			$formBuilder->build();
			$form = $formBuilder->form();
			$formHandler = new FormHandler($form, $this->managers->getManagerOf('News'), $request);
			if ($formHandler->process())
			{
				self::$app->user()->setFlash($news->isNew() ? 'La news a bien été ajoutée !' : 'La news a bien été modifiée !');

				self::$app->httpResponse()->redirect(NewsController::getLinkToBuildIndex());
			}
			$this->page->addVar('form', $form->createView());
		}

	}

	/**
	 * Renvoie le lien de la page d'accueil Backend
	 *
	 * @return string
	 */
	static public function getLinkToBuildIndex() {
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'index' );
	}

	/**
	 * Renvoie le lien de la page de maj d'une news
	 * @param News $news
	 *
	 * @return string
	 */
	static public function getLinkToPutUpdateNews(News $news) {
		$id = $news->id();
		if (empty($id)) {
			throw new \RuntimeException('Impossible de creer le lien du Comment : L\'id du Comment n\'est pas renseigné !');
		}
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'update', array('id' => (int)$news->id()) );
	}

	/**
	 * Renvoie le lien de la page de suppression d'une news
	 * @param News $news
	 *
	 * @return string
	 */
	static public function getLinkToDeleteNews(News $news) {
		$id = $news->id();
		if (empty($id)) {
			throw new \RuntimeException('Impossible de creer le lien du Comment : L\'id du Comment n\'est pas renseigné !');
		}
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'delete', array('id' => (int)$news->id()) );
	}

	/**
	 * Renvoie le lien de la page de creation d'une news
	 * @return string
	 */
	static public function getLinkToPutInsertNews() {
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'insert' );
	}

	/**
	 * Renvoie le lien de la page de maj d'un Comment
	 * @param Comment $Comment
	 *
	 * @return string
	 */
	static public function getLinkToPutUpdateComment(Comment $Comment) {
		$id = $Comment->id();
		if (empty($id)) {
			throw new \RuntimeException('Impossible de creer le lien du Comment : L\'id du Comment n\'est pas renseigné !');
		}
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'updateComment', array('id' => (int)$Comment->id()) );
	}
	
	static public function getLinkToPutUpdateCommentFromAjax( Comment $Comment ) {
		$id = $Comment->id();
		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Can\'t create Comment link : Comment id is unknown !' );
		}
		
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'putUpdateCommentFromAjax', array( 'id' => (int)$id ) );
	}

	/**
	 * Renvoie le lien de la page de suppression d'un Comment
	 * @param Comment $Comment
	 *
	 * @return string
	 */
	static public function getLinkToDeleteComment(Comment $Comment) {
		$id = $Comment->id();
		if (empty($id)) {
			throw new \RuntimeException('Impossible de creer le lien du Comment : L\'id du Comment n\'est pas renseigné !');
		}
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'deleteComment', array('id' => (int)$Comment->id()) );
	}
	
}


