<?php
/**
 * Created by PhpStorm.
 * User: bnzonlia
 * Date: 28/04/2017
 * Time: 15:09
 */

namespace Helpers;

/**
 * Trait MenuHelper
 *
 * Helper contenant les méthodes pour gérer les liens
 *
 */
trait MenuHelper {
	
	/**
	 * Ajoute un lien au  given array, et retourne le tableau par reference
	 *
	 * @param string[][] $link_a tableau de tous les liens generés
	 * @param string $url Url
	 * @param string $label Label
	 * @param string $js_function_name
	 *
	 * @return string[][] Array of all links already generated
	 */
	static public function &addLink(&$link_a, $url, $label = '', $js_function_name = null) {
		if (!is_string($label)) {
			throw new \InvalidArgumentException('Label must be a string !');
		}
		if (!is_string($url) OR empty($url)) {
			throw new \InvalidArgumentException('Url must be a non-empty string !');
		}
		$new_link_a = ['label' => $label,
					   'url' => $url];
		
		if (null !== $js_function_name) {
			if (!is_string($js_function_name) OR empty($js_function_name) OR ctype_digit($js_function_name[0])) {
				throw new \InvalidArgumentException('JS function name must be a non empty string beginning with "_" or a letter !');
			}
			$new_link_a['js_function_name'] = $js_function_name;
		}
		$link_a[] = $new_link_a;
		return $link_a;
	}
}