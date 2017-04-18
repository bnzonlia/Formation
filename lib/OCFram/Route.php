<?php
namespace OCFram;
/**
 * Class Route
 * Modélise une route
 *
 * @package OCFram
 */
class Route {
	use Hydrator;
	/**
	 * @var $action string
	 */
	protected $action;
	/**
	 * @var $module string
	 */
	protected $module;
	/**
	 * @var $url string
	 */
	protected $url;
	/**
	 * @var $varsNames string[] Noms des variables nécessaires à la route
	 */
	protected $varsNames;
	/**
	 * @var $vars array Valeurs des variables nécessaires à la route
	 */
	protected $vars;
	/**
	 * @var $format string Format d'affichage de la page (html, json, etc.)
	 */
	protected $format;

	/**
	 * Construit une nouvelle route en l'hydratant.
	 *
	 * @param array $attributes Tableau associatif attribut-valeur
	 */
	public function __construct( array $attributes ) {
		$this->hydrate( $attributes );
		if ( !isset( $this->vars ) ) {
			$this->setVars( array() );
		}
	}

	/**
	 * Vérifie si la route a besoin de variables pour que la page soit trouvée.
	 *
	 * @return bool
	 */
	public function hasVars() {
		return !empty( $this->varsNames );
	}

	/**
	 * Vérifie si l'url fournie correspond à l'url de la route (présente dans le fichier routes.xml).
	 * Si oui, renvoie les paramètres passés à la route.
	 *
	 * @param $url string
	 *
	 * @return false|string[]
	 */
	public function match( $url ) {
		if ( preg_match( '%^' . $this->url . '$%', $url, $matches ) ) {
			return $matches;
		}

		return false;
	}

	/**
	 * Setter pour l'attribut action.
	 *
	 * @param $action string
	 */
	public function setAction( $action ) {
		if ( is_string( $action ) ) {
			$this->action = $action;
		}
	}

	/**
	 * Setter pour l'attribut module.
	 *
	 * @param $module string
	 */
	public function setModule( $module ) {
		if ( is_string( $module ) ) {
			$this->module = $module;
		}
	}

	/**
	 * Setter pour l'attribut url.
	 *
	 * @param $url string
	 */
	public function setUrl( $url ) {
		if ( is_string( $url ) ) {
			$this->url = $url;
		}
	}

	/**
	 * Setter pour l'attribut varsNames.
	 *
	 * @param string[] $varsNames Noms des variables nécessaires à la route
	 */
	public function setVarsNames( array $varsNames ) {
		$this->varsNames = $varsNames;
	}

	/**
	 * Setter pour l'attribut vars.
	 *
	 * @param array $vars Valeurs des variables nécessaires à la route
	 */
	public function setVars( array $vars ) {
		$this->vars = $vars;
	}

	/**
	 * Setter pour l'attribut format
	 *
	 * @param string $format Format de la page (html, json, etc.)
	 */
	public function setFormat($format) {
		$this->format = $format;
	}


	/**
	 * @return string
	 */
	public function action() {
		return $this->action;
	}

	/**
	 * @return string
	 */
	public function module() {
		return $this->module;
	}

	/**
	 * @return string
	 */
	public function url() {
		return $this->url;
	}

	/**
	 * @return \string[]
	 */
	public function varsNames() {
		return $this->varsNames;
	}

	/**
	 * @return array
	 */
	public function vars() {
		return $this->vars;
	}

	/**
	 * @return string
	 */
	public function format() {
		return $this->format;
	}

}