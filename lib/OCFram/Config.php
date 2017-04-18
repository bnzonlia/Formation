<?php
namespace OCFram;
class Config extends ApplicationComponent {
	/**
	 * @var $vars array Liste des variables de configuration
	 */
	protected $vars;

	/**
	 * @param Application $app
	 */
	public function __construct( Application $app ) {
		parent::__construct( $app );
		$this->vars = array();
	}

	/**
	 * Récupère les variables de configuration associées au module.
	 *
	 * @param $var string
	 *
	 * @return mixed|null
	 */
	public function get( $var ) {
		if ( empty( $vars ) ) // Fulfill $vars by parsing XML
		{
			$xml = new \DOMDocument();
			$xml->load( __DIR__ . '/../../App/' . self::$app->name() . '/Config/app.xml' );
			$data = $xml->getElementsByTagName( 'define' );
			foreach ( $data as $value ) {
				/**
				 * @var $value \DOMElement
				 */
				if ( $value->hasAttribute( 'var' ) AND $value->hasAttribute( 'value' ) ) {
					$vars[ $value->getAttribute( 'var' ) ] = $value->getAttribute( 'value' );
				}
			}
		}

		if ( !isset( $vars[ $var ] ) ) {
			return null;
		}

		return $vars[ $var ];
	}
}