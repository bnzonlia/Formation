<?php

namespace App\Backend\Modules\News;

use Model\NewsManagerPDO;
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\News;
use \Entity\User;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \FormBuilder\NewsFormBuilder;
use \OCFram\FormHandler;

class NewsController extends BackController {
	public function executeDelete( HTTPRequest $request ) {
		
		$newsId = $request->getData( 'id' );
		
		/** je recupère les informations du membre connecté */
		$user = $this->app->user()->getAttribute( 'Member' );
		$manager=$this->managers->getManagerOf( 'News' );
		$news= $manager->getUnique($newsId);

		/** si le user est un admin supreme il supprime */
		if ($user->membertype()== 1 ) {
			$this->managers->getManagerOf( 'News' )->delete( $newsId );
			$this->managers->getManagerOf( 'Comments' )->deleteFromNews( $newsId );
			$this->app->user()->setFlash( 'La news a bien été supprimée !' );
			$this->app->httpResponse()->redirect( '.' );
		}
		/** sinon , c'est un admin partiel et il ne peut que supprimer sa news */
		 elseif ($user->membertype()== 0 && $user->id() == $news->auteur())
		 {
			 $this->managers->getManagerOf( 'News' )->delete( $newsId );
			 $this->managers->getManagerOf( 'Comments' )->deleteFromNews( $newsId );
			 $this->app->user()->setFlash( 'La news a bien été supprimée !' );
			 $this->app->httpResponse()->redirect( '.' );
		 }
		else {
			$this->app->user()->setFlash( 'vous avez pas le droit de supprimer!' );
			$this->app->httpResponse()->redirect('.');
		}
	}
	
	public function executeDeleteComment( HTTPRequest $request ) {

		/** je recupère les informations du membre connecté */
		$user = $this->app->user()->getAttribute( 'Member' );
		$manager=$this->managers->getManagerOf( 'Comments' );
		$com= $manager->get($request->getData( 'id' ));
		/** si le user est un admin supreme il supprime */
		if ($user->membertype()== 1 ) {

			$this->managers->getManagerOf( 'Comments' )->delete( $request->getData( 'id' ) );
			$this->app->user()->setFlash( 'Le commentaire a bien été supprimé !' );
			$this->app->httpResponse()->redirect( '.' );
		}
		/** sinon , c'est un admin partiel et il ne peut que supprimer son commentaire */
		elseif ($user->membertype()== 0 && $user->id() == $com->auteur())
		{
			$this->managers->getManagerOf( 'Comments' )->delete( $request->getData( 'id' ) );
			$this->app->user()->setFlash( 'Le commentaire a bien été supprimé !' );
			$this->app->httpResponse()->redirect( '.' );
		}
		else {
			$this->app->user()->setFlash( 'vous avez pas le droit de supprimer!' );
			$this->app->httpResponse()->redirect('.');
		}

	}
	
	public function executeIndex( HTTPRequest $request ) {
		$this->page->addVar( 'title', 'Gestion des news' );
		
		/** @var NewsManagerPDO $manager */
		$manager = $this->managers->getManagerOf( 'News' );
		
		$this->page->addVar( 'listeNews', $manager->getList() );
		$this->page->addVar( 'nombreNews', $manager->count() );
	}
	
	public function executeInsert( HTTPRequest $request ) {
		$this->processForm( $request );
		
		$this->page->addVar( 'title', 'Ajout d\'une news' );
	}
	
	public function executeUpdate( HTTPRequest $request ) {
		$this->processForm( $request );
		
		$this->page->addVar( 'title', 'Modification d\'une news' );
	}
	
	public function executeUpdateComment( HTTPRequest $request ) {
		$this->page->addVar( 'title', 'Modification d\'un commentaire' );

		/** je recupère les informations du membre connecté */
		$user = $this->app->user()->getAttribute( 'Member' );
		$manager=$this->managers->getManagerOf( 'Comments' );
		$com= $manager->get($request->getData( 'id' ));

		/** si le user est un admin supreme il modifie tous Les commentaires */
		if ($user->membertype()== 1 ) {

			if ( $request->method() == 'POST' ) {
				$comment = new Comment( [
					'id'      => $request->getData( 'id' ),
					'auteur'  => $request->postData( 'auteur' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
			}
			else {
				$comment = $this->managers->getManagerOf( 'Comments' )->get( $request->getData( 'id' ) );
			}

			$formBuilder = new CommentFormBuilder( $comment );
			$formBuilder->build();

			$form = $formBuilder->form();

			$formHandler = new FormHandler( $form, $this->managers->getManagerOf( 'Comments' ), $request );

			if ( $formHandler->process() ) {
				$this->app->user()->setFlash( 'Le commentaire a bien été modifié' );

				$this->app->httpResponse()->redirect( '/admin/' );
			}

			$this->page->addVar( 'form', $form->createView() );
		}
		/** sinon , c'est un admin partiel et il ne peut que supprimer son commentaire */
		elseif ($user->membertype()== 0 && $user->id() == $com->auteur())
		{

			if ( $request->method() == 'POST' ) {
				$comment = new Comment( [
					'id'      => $request->getData( 'id' ),
					'auteur'  => $request->postData( 'auteur' ),
					'contenu' => $request->postData( 'contenu' ),
				] );
			}
			else {
				$comment = $this->managers->getManagerOf( 'Comments' )->get( $request->getData( 'id' ) );
			}

			$formBuilder = new CommentFormBuilder( $comment );
			$formBuilder->build();

			$form = $formBuilder->form();

			$formHandler = new FormHandler( $form, $this->managers->getManagerOf( 'Comments' ), $request );

			if ( $formHandler->process() ) {
				$this->app->user()->setFlash( 'Le commentaire a bien été modifié' );

				$this->app->httpResponse()->redirect( '/admin/' );
			}

			$this->page->addVar( 'form', $form->createView() );
		}
		else {
			$this->app->user()->setFlash( 'vous avez pas le droit de modifier!' );
			$this->app->httpResponse()->redirect( '/admin/' );
		}

	}
	
	public function processForm( HTTPRequest $request ) {


		/** je recupère les informations du membre connecté */
		$user = $this->app->user()->getAttribute( 'Member' );
		$manager=$this->managers->getManagerOf( 'News' );

			if ( $request->method() == 'POST' ) {
				$news = new News( [
					'auteur'  => $request->postData( 'auteur' ),
					'titre'   => $request->postData( 'titre' ),
					'contenu' => $request->postData( 'contenu' ),
				] );

				if ( $request->getExists( 'id' ) ) {
					$news->setId( $request->getData( 'id' ) );
				}
			}
			else {
				// L'identifiant de la news est transmis si on veut la modifier
				if ( $request->getExists( 'id' ) ) {
					$news = $this->managers->getManagerOf( 'News' )->getUnique( $request->getData( 'id' ) );
					$com=$manager->getUnique($request->getData( 'id' ));
					if ($user->membertype()==0  && $user->id() == $com->auteur() )
					{

					}
					 else{
						 $this->app->user()->setFlash( ' vous avez pas le droit de modfier');
						 $this->app->httpResponse()->redirect( '/admin/' );
					 }
				}
				else {
					$news = new News;
				}
			}

			$formBuilder = new NewsFormBuilder( $news );
			$formBuilder->build();

			$form = $formBuilder->form();

			$formHandler = new FormHandler( $form, $this->managers->getManagerOf( 'News' ), $request );

			if ( $formHandler->process() ) {
				$this->app->user()->setFlash( $news->isNew() ? 'La news a bien été ajoutée !' : 'La news a bien été modifiée !' );

				$this->app->httpResponse()->redirect( '/admin/' );
			}

			$this->page->addVar( 'form', $form->createView() );
		}
}