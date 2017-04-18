<?php
namespace OCFram;
/**
 * Class Page
 * Classe représentant la page générée et renvoyée à l'utilisateur.
 *
 * @package OCFram
 */
class Page extends ApplicationComponent {
	/**
	 * @var $contentFile string Chemin relatif vers le fichier de vue
	 */
	protected $contentFile;
	/**
	 * @var $vars array Tableau associatif donnant à chaque variable de la vue sa valeur
	 */
	protected $vars;
	/**
	 * @var $format string Format de la page (html, json, etc.)
	 */
	protected $format;

	/**
	 * Construit une page vierge à partir de l'application choisie
	 *
	 * @param Application $app
	 * @param string      $format         Format de la page à afficher (html, json, etc.)
	 */
	public function __construct( Application $app, $format = 'html') {
		parent::__construct( $app );
		$this->contentFile    = '';
		$this->vars           = array();
		$this->format         = $format;
	}

	/**
	 * Ajoute une variable à la page.
	 *
	 * @param $var   string Nom de la variable
	 * @param $value mixed Valeur de la variable
	 */
	public function addVar( $var, $value ) {
		if ( !is_string( $var ) OR is_numeric( $var ) OR empty( $var ) ) {
			throw new \InvalidArgumentException( 'Variable name must be a non NULL string' );
		}
		$this->vars[ $var ] = $value;
	}

	/**
	 * Génère le code html associé à la page courante.
	 *
	 * @return string
	 */
	public function getGeneratedPage() {
		if ( !file_exists( $this->contentFile ) ) {
			var_dump($this->contentFile);
			throw new \RuntimeException( 'Specified view "' . $this->contentFile . '" doesn\'t exists' );
		}


		switch( $this->format() ) {

			case 'json':
				return $this->getGeneratedPageJSON();
			default:
				return $this->getGeneratedPageHTML();
		}


	}

	/**
	 * Fonction qui va gerer la rendu HTML d'une page
	 */
	private function getGeneratedPageHTML() {
		/*
		 * @var User $User utilisée dans les vues
		 */
		/** @noinspection PhpUnusedLocalVariableInspection */
		$User = self::$app->user();
		
		extract( $this->vars );


		// Créer la page en bufferisation
		ob_start();
		require $this->contentFile; // Existence du fichier vérifiée
		/**
		 * @var $content string utilisée dans les vues
		 */

		/** @noinspection PhpUnusedLocalVariableInspection */
		$content = ob_get_clean(); // Injecter le contenu de la page interne dans le layout

		ob_start();

		require __DIR__ . '/../../App/' . self::$app->name() . '/templates/layout.' . $this->format . '.php'; // Construction dynamique du chemin de layout OK

		return ob_get_clean();
	}

	/**
	 * Fonction qui va gerer la rendu JSON d'une page
	 */
	private function getGeneratedPageJSON() {
		/*
	 * @var User $User utilisée dans les vues
	 */
		/** @noinspection PhpUnusedLocalVariableInspection */
		$User = self::$app->user();

		//		if ( $this->format == 'json' ) {
		//			// On serialize toutes les Entity passées en paramètre
		//			foreach ( $this->vars as &$element ) {
		//				if ( $element instanceof Entity ) {
		//					$element = json_encode( $element );
		//				}
		//			}
		//		}
		extract( $this->vars );

		// Créer la page en bufferisation
		/** @noinspection PhpUnusedLocalVariableInspection */
		$content = require $this->contentFile; // Existence du fichier vérifiée

		/**
		 * @var $content string utilisée dans les vues
		 */

		return json_encode(require(__DIR__ . '/../../App/' . self::$app->name() . '/templates/layout.' . $this->format . '.php')) ;
	}


	/**
	 * Setter pour l'attribut contentFile.
	 *
	 * @param $contentFile string Chemin relatif vers le fichier de vue
	 */
	public function setContentFile( $contentFile ) {
		if ( !is_string( $contentFile ) OR empty( $contentFile ) ) {
			throw new \InvalidArgumentException( 'View file name must be a non NULL string' );
		}
		$this->contentFile = $contentFile;
	}

	/**
	 * @return string
	 */
	public function format() {
		return $this->format;
	}

	/**
	 * @return array
	 */
	public function vars() {
		return $this->vars;
	}
}