<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 12:42
 */
namespace OCFram;
/**
 * Trait Hydrator
 * Exploite une méthode générique d'hydratation des objets.
 *
 * @package OCFram
 */
trait Hydrator {
	/**
	 * Hydrate l'objet sur lequel est appelé hydrate(),
	 * en settant les différentes valeurs passées en tableau associatif.
	 *
	 * @param array $values tableau clé-valeur où les clés sont des attributs de l'objet
	 */
	public function hydrate( array $values ) {
		foreach ( $values as $key => $argument ) {
			$method = 'set' . ucfirst( $key );
			if ( is_callable( array(
				$this,
				$method,
			) ) ) {
				$this->$method( $argument );
			}
		}
	}
}