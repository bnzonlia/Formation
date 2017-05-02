<?php
namespace OCFram;
/**
 * Class Application
 *
 * Classe modélisant une des applications du site.
 *
 * Une seule application est lancée à la fois (singleton)
 *
 * @package OCFram
 */
abstract class Application {
	/**
	 * @var $httprequest HTTPRequest Requête envoyée par le client
	 */
	protected $httpRequest;
	/**
	 * @var $httpResponse HTTPResponse Page renvoyée au client par le serveur
	 */
	protected $httpResponse;
	/**
	 * @var $name string Nom de l'application
	 */
	protected $name;
	/**
	 * @var $user User Objet donnant les attributs de la session client
	 */
	protected $user;
	/**
	 * @var $config Config Objet donnant les variables de configuration serveur
	 */
	protected $config;
	/**
	 * @var $Router Router Routeur qui contient toutes les routes déjà générées
	 */
	protected $Router;
	
	/**
	 * Construit un objet application en initialisant httpRequest et httpResponse
	 *
	 * @return Application
	 *
	 * @throws \RuntimeException
	 */
	public function __construct() {
		// On vérifie si l'instance d'application existe déjà : pour cela on regarde un attribut différent du name qu'on vient de set
		if (!isset ($this->httpRequest)) {
			// On construit l'instance unique
			$this->httpRequest  = new HTTPRequest( $this );
			$this->httpResponse = new HTTPResponse( $this );
			$this->user         = new User( $this );
			$this->config       = new Config( $this );
			$this->Router       = new Router( $this );
			$this->name = static::getAppName();
		}
		return $this;
	}
	
	/**
	 * Methode permettant de lancer une application
	 */
	abstract public function run();
	
	/**
	 * Récupère un contrôleur à partir d'une url demandée dans httpRequest
	 *
	 * @return \OCFram\BackController
	 */
	public function getController() {
		// Si le routeur appelé n'est pas créé, on le crée.
		if ( !isset( Router::routes()[$this->name] ) ) {
			Router::generateRoutes($this->name);
		}
		
		// 2) Une fois toutes les routes créées, essayer de router l'URL reçue
		try {
			/**
			 * @var $route \OCFram\Route
			 */
			$route = $this->Router->getRoute( $this->httpRequest()->requestURI() );

			// 3) Ajouter les variables lues dans l'url au tableau _GET
			// En effet ce sont des variables récupérées par l'url
			$_GET = array_merge( $_GET, $route->vars() );

			// 4) Instanciation du contrôleur
			$controllerClass = 'App\\' . $this->name . '\\Modules\\' . $route->module() . '\\' . $route->module() . 'Controller';
			return new $controllerClass( $this, $route->module(), $route->action(), $route->format());

		}
		catch ( \RuntimeException $e ) {
			if ( $e->getCode() == Router::ROUTE_NOT_FOUND ) // Si on n'a pas trouvé la route, erreur 404
			{
				$this->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, $e );
			}
		}
		
		// Pas de retour ici
		return null;
	}
	
	
	/**
	 * @return HTTPRequest
	 */
	public function httpRequest() {
		return $this->httpRequest;
	}
	
	/**
	 * @return HTTPResponse
	 */
	public function httpResponse() {
		return $this->httpResponse;
	}
	
	/**
	 * @return string
	 */
	public function name() {
		return $this->name;
	}
	
	/**
	 * @return User
	 */
	public function user() {
		return $this->user;
	}
	
	/**
	 * @return Config
	 */
	public function config() {
		return $this->config;
	}
	
	/**
	 * Donne le nom de l'application associée à la méthode appelante.
	 *
	 * @return string
	 */
	static public function getAppName() {
		// On construit dynamiquement le nom de l'application
		$real_class = get_called_class();
		$short_class_name_without_application = preg_replace("/Application$/", "", substr(strrchr($real_class, '\\'), 1));
		if ($short_class_name_without_application  === NULL) {
			throw new \RuntimeException('Illegal class name '.$real_class);
		}
		return $short_class_name_without_application;
	}
}