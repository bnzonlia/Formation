<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 09:56
 */
namespace OCFram;
/**
 * Class BackController
 *
 * Modélise un contrôleur de l'application.
 *
 * @package OCFram
 */
abstract class BackController extends ApplicationComponent {
	/**
	 * @var $action string Nom de l'action associée au contrôleur
	 */
	protected $action;
	/**
	 * @var $module string Nom du module associé au contrôleur
	 */
	protected $module;
	/**
	 * @var $page Page page associée au contrôleur
	 */
	protected $page;
	/**
	 * @var $view string Adresse relative de la vue à utiliser pour ce contrôleur
	 */
	protected $view;
	/**
	 * @var $managers Managers Liste des mabagers existants pour ce module
	 */
	protected $managers;
	
	/**
	 * Construit un backController comme une composant de l'application.
	 * Le backController est associé à une action et un module, et construit une vue.
	 * La classe construisant l'objet DAO asociée à la daoClass doit s'appeler [daoClass]Factory et se situer dans le répertoire OCFram.
	 * La méthode de cette classe fournissant l'objet DAO doit s'appeler get[DAO}
	 *
	 * @param Application $app
	 * @param string      $module
	 * @param string      $action
	 * @param string      $format         Format de la page à construire
	 * @param string      $daoClass       Nom de la classe permettant de construire le DAO
	 */
	public function __construct( Application $app, $module, $action, $format, $daoClass = 'PDO' ) {
		parent::__construct( $app );
		$this->setAction( $action );
		$this->setModule( $module );
		$this->page = new Page( $app, $format );
		// Créer la page de réponse et la relier à la page du BackController
		self::$app->httpResponse()->setPage($this->page);
		$this->setView( $action );
		
		
		// Calcul du nom de la classe construisant le DAO
		$daoFactoryClass = 'OCFram\\' . $daoClass . 'Factory';
		// Calcul du nom de la méthode retournant le DAO
		$daoMethod = 'get' . ucfirst( $daoClass );
		
		$this->managers = new Managers( $daoClass, $daoFactoryClass::$daoMethod( ) );
	}
	
	/**
	 * Exécute l'action associée au contrôleur.
	 */
	public function execute() {
		$method = 'execute' . ucfirst( $this->action );
		if ( !is_callable( [
			$this,
			$method,
		] )
		) {
			throw new \RuntimeException( 'Undefined action "' . $this->action . '" on this module' );
		}
		$this->$method( self::$app->httpRequest() );
	}
	
	/**
	 * @return Page
	 */
	public function page() {
		return $this->page;
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
	public function action() {
		return $this->action;
	}
	
	/**
	 * Setter pour l'attribut module.
	 *
	 * @param $module string
	 */
	public function setModule( $module ) {
		if ( !is_string( $module ) || empty( $module ) ) {
			throw new \InvalidArgumentException( 'Module must be a valid string' );
		}
		$this->module = $module;
	}
	
	/**
	 * Setter pour l'attribut action.
	 *
	 * @param $action string
	 */
	public function setAction( $action ) {
		if ( !is_string( $action ) || empty( $action ) ) {
			throw new \InvalidArgumentException( 'Action must be a valid string' );
		}
		$this->action = $action;
	}
	
	/**
	 * Setter pour l'attribut view.
	 * Il définit le fichier de vue.
	 *
	 * @param $view string
	 */
	public function setView( $view ) {
		if ( !is_string( $view ) || empty( $view ) ) {
			throw new \InvalidArgumentException( 'View must be a valid string' );
		}
		$this->view = $view;
		$this->page->setContentFile( __DIR__ . '/../../App/' . self::$app->name() . '/Modules/' . $this->module . '/Views/' . $this->view . '.' . $this->page->format() . '.php' );
	}
	
	/**
	 * Donne le nom du module associé au contôleur appelant.
	 *
	 * @return string
	 */
	static public function getModuleName() {
		// On construit dynamiquement le nom de la classe
		$real_class = get_called_class();
		$short_class_name_without_controller = preg_replace("/Controller$/", "", substr(strrchr($real_class, '\\'), 1));
		if ($short_class_name_without_controller === NULL) {
			throw new \RuntimeException('Illegal Controller name '.$real_class);
		}
		return $short_class_name_without_controller;
	}

	public function managers() {
		return $this->managers;
	}
	
}