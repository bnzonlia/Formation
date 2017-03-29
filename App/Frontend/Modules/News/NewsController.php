<?php
namespace App\Frontend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \OCFram\FormHandler;

class NewsController extends BackController
{
	public function executeIndex(HTTPRequest $request) {
		$nombreNews       = $this->app->config()->get( 'nombre_news' );
		$nombreCaracteres = $this->app->config()->get( 'nombre_caracteres' );
		
		// On ajoute une définition pour le titre.
		$this->page->addVar( 'title', 'Liste des ' . $nombreNews . ' dernières news' );
		
		// On récupère le manager des news.
		$manager = $this->managers->getManagerOf( 'News' );
		
		$listeNews = $manager->getList( 0, $nombreNews );
		
		foreach ( $listeNews as $News ) {
			if ( strlen( $News->contenu() ) > $nombreCaracteres ) {
				$debut = substr( $News->contenu(), 0, $nombreCaracteres );
				$debut = substr( $debut, 0, strrpos( $debut, ' ' ) ) . '...';
				
				$News->setContenu( $debut );
			}
		}
		$this->page->addVar( 'listeNews', $listeNews );
	}
	
	public function executeShow(HTTPRequest $request)
	{
		$news = $this->managers->getManagerOf('News')->getUnique($request->getData('id'));

		if (empty($news))
		{
			$this->app->httpResponse()->redirect404();

		}
		$this->page->addVar('title', $news['titre']);
		$this->page->addVar('news', $news);
		$this->page->addVar('comments', $this->managers->getManagerOf('Comments')->getListOf($news->id()));


	}
	
	 public function executeInsertComment(HTTPRequest $request)
	 {
		 // Si le formulaire a été envoyé.
		 if ($request->method() == 'POST')
		 {
			 $comment = new Comment([
				 'news' => $request->getData('news'),
				 'auteur' => $request->postData('auteur'),
				 'contenu' => $request->postData('contenu')
			 ]);
		 }
		 else
		 {
			 $comment = new Comment;
		 }
		
		 $formBuilder = new CommentFormBuilder($comment);
		 $formBuilder->build();
		
		 $form = $formBuilder->form();
		
		 if ($request->method() == 'POST' && $form->isValid())
		 {
			 $this->managers->getManagerOf('Comments')->save($comment);
			 $this->app->user()->setFlash('Le commentaire a bien été ajouté, merci !');
			 $this->app->httpResponse()->redirect('news-'.$request->getData('news').'.html');
		 }
		
		 $this->page->addVar('comment', $comment);
		 $this->page->addVar('form', $form->createView());
		 $this->page->addVar('title', 'Ajout d\'un commentaire');
	 }
}