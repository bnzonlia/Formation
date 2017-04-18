<?php
namespace OCFram;
/**
 * Class ApplicationComponent
 *
 * Modélise un composant de l'application.
 *
 * @package OCFram
 */
abstract class ApplicationComponent {
	/**
	 * @var $app Application
	 */
	static protected $app;

	/**
	 * @param Application $app
	 */
	public function __construct( Application $app ) {
		self::$app = $app;
	}

	/**
	 * @return Application
	 */
	static public function app() {
		return self::$app;
	}
}