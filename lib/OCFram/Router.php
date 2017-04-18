<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 30/09/2016
 * Time: 16:41
 */
namespace OCFram;
/**
 * Class Router
 *
 * Modélise le routeur, chargé de la gestion des routes et de rediriger le client vers la bonne route pour récupérer la page souhaitée.
 *
 * Il n'y a qu'un seul routeur (singleton)
 *
 * @package OCFram
 */
class Router extends ApplicationComponent {
	/**
	 * @var $routes Route[][]
	 */
	static protected $routes = array();
	const ROUTE_NOT_FOUND = 18;

	/**
	 * Génère les routes associées à une application de nom passé en paramètre
	 *
	 * @param $app_name string Nom de l'application dont les routes doivent être générées
	 */
	static public function generateRoutes( $app_name ) {
		/**
		 * @var $route \DOMElement
		 */
		// 1) Aller chercher dans la liste des routes toutes les routes existantes
		$xml = new \DOMDocument();
		$xml->load( __DIR__ . '/../../App/' . $app_name . '/Config/routes.xml' );
		$route_list = $xml->getElementsByTagName( 'route' );
		foreach ( $route_list as $route ) {

			// Récupérer les arguments nécessaires à la route
			$vars = $route->hasAttribute( 'vars' ) ? explode( ',', $route->getAttribute( 'vars' ) ) : array();
			// Le format par défaut d'une route est le format html
			$format = $route->hasAttribute( 'format' ) ? $route->getAttribute( 'format' ) : 'html';

			// Ajouter la route au routeur les arguments passés
			self::addRoute( $app_name, new Route( array(
				'action'         => $route->getAttribute( 'action' ),
				'module'         => $route->getAttribute( 'module' ),
				'url'            => $route->getAttribute( 'url' ),
				'varsNames'      => $vars,
				'format'         => $format,
			) ) );
			
		}
	}

	/**
	 * Ajoute la route au catalogue du routeur, si elle n'existe pas déjà.
	 *
	 * @param string $app_name Nom de l'application à laquelle sont associées les routes
	 * @param Route  $route
	 */
	static public function addRoute( $app_name, Route $route ) {
		if ( !isset( self::$routes[ $app_name ] ) OR !in_array( $route, self::$routes[ $app_name ] ) ) {
			self::$routes[ $app_name ][] = $route;
		}
	}

	/**
	 * Récupère une URL à partir du nom du module et de l'action souhaitée.
	 * Si l'URL à récupérer nécessite des paramètres, ils sont indiqués dans given_values_a.
	 *
	 * @param string $app            Nom de l'application (Frontend, Backend...)
	 * @param string $module         Le module souhaité
	 * @param string $action         L'action souhaitée
	 * @param array  $given_values_a Les variables nécessaires dans l'Url
	 *
	 * @return string L'URL calculée
	 */
	static public function getUrlFromModuleAndAction( $app, $module, $action, $given_values_a = array() ) {
		if ( !isset( self::$routes[ $app ] ) ) {
			// Si les routes d' l'application n'existent pas, on les crée et on les rajoute.
			self::generateRoutes( $app );
		}

		// 1) Aller chercher dans la liste des routes toutes les routes existantes
		foreach ( self::$routes[ $app ] as $Route ) {
			/**
			 * @var $Route Route
			 */
			if ( $Route->module() === $module && $Route->action() === $action ) {
				// On a trouvé un module et une action qui correspondent : on vérifie si on a le bon nombre de paramètres
				$route_attribute_count = count( $Route->varsNames() );
				if ( count( $given_values_a ) === $route_attribute_count ) {
					// En plus elle a le bon nombre d'attributs
					// Prendre l'url
					$url = $Route->url();
					if ( 0 != $route_attribute_count ) {
						// Rechercher les parties variables : elles sont indiquées par des parenthèses dans l'URL
						preg_match( '/\(.+\)/', $url, $pattern_a );
						// Associer les clés des noms de variables aux parties variables
						$replacement_a = array_combine( $Route->varsNames(), $pattern_a );
						foreach ( $replacement_a as $var_name => $pattern ) {
							// Si le pattern est respecté, alors on remplace l'élément correspondant dans l'URL
							// On vérifie d'abord s'il est bien set.
							if ( !isset( $given_values_a[ $var_name ] ) ) {
								throw new \InvalidArgumentException( 'Le paramètre ' . $var_name . ' n\'est pas renseigné et est nécessaire au fonctionnement de la route.' );
							}
							if ( preg_match( '/^' . $pattern . '$/', $given_values_a[ $var_name ] ) ) {
								$url = preg_replace( '/\(.+\)/', $given_values_a[ $var_name ], $url, 1 );
							}
							else {
								throw new \InvalidArgumentException( 'Les paramètres de la route ne correspondent pas aux paramètres indiqués dans la configuration.' );
							}
						}
					}

					// Remplacer les points échappées par des points et renvoyer l'URL calculée.
					return preg_replace( '/\\\./', '.', $url );
				}
			}
		}
		// Si on n'a pas trouvé, c'est que la route est incorrecte
		throw new \InvalidArgumentException( 'Impossible de trouver l\'action ' . $action . ' dans le module ' . $module . ' de l\'application ' . $app .'. Vérifiez le nom de l\'application, du module et de l\'action, ainsi que les paramètres.' );
	}

	/**
	 * @return Route[][]
	 */
	static public function routes() {
		return self::$routes;
	}

	/**
	 * Récupère la route qui correspond à l'url fournie en paramètre.
	 * Une erreur d'exécution est renvoyée si la route n'existe pas.
	 *
	 * @param $url string
	 *
	 * @return Route
	 */
	public function getRoute( $url ) {
		// Trouver la route qui matche l'url fournie
		foreach ( self::$routes[ self::$app->name() ] as $route ) {
			$varsValues = $route->match( $url );
			if ( $varsValues !== false AND $route->hasVars() ) // Si on a des variables, on doit les récupérer pour les faire transiter dans l'URL
			{
				$varsNames = $route->varsNames();
				$listVars  = array();
				foreach ( $varsValues as $key => $value ) // Récupérer les valeurs des attributs en clé-valeur entre $varsNames et $varsValues
				{
					if ( $key > 0 ) // Le premier retour de preg_match est la chaîne complète
					{
						$listVars[ $varsNames[ $key - 1 ] ] = $value;
					}
				}
				$route->setVars( $listVars );
			}
			// Si c'est la bonne route, la renvoyer
			if ( $varsValues !== false ) {
				return $route;
			}
		}

		// On n'a pas trouvé : erreur
		throw new \RuntimeException( 'Couldn\'t find route ' . $url . ', no such route exists !', Router::ROUTE_NOT_FOUND );
	}

	public function resetRouter() {
		self::$routes = array();
		self::generateRoutes( self::$app->name() );
	}
}