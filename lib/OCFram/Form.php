<?php
namespace OCFram;

/**
 * Class Form
 *
 * Représente le contenu d'un formulaire. Le formulaire est associé à une \OCFram\Entity et contient un tableau de \OCFram\Fields.
 *
 * @package OCFram
 */
class Form {
	/**
	 * @var $entity Entity
	 */
	protected $entity;
	/**
	 * @var $Field_a Field[]
	 */
	protected $Field_a;
	
	/**
	 * Form constructor.
	 *
	 * @param Entity $entity
	 */
	public function __construct( Entity $entity ) {
		$this->setEntity( $entity );
		$this->Field_a = array();
	}
	
	/**
	 * Ajoute un champ au formulaire passé en paramètre, en assignant la valeur du champ associé de l'netité du formulaire ($this -> entity -> $field_name()).
	 * Retourne le nouveau formulaire.
	 *
	 * @param Field $field
	 *
	 * @return Form
	 */
	public function add( Field $field ) {
	$field_name = $field->name(); // Récupérer le nom du champ
	// La value du field est initialisée à la valeur détenue par l'entité
	$this->Field_a[] = $field;

		if (is_callable([$this->entity,$field_name])) { // s'il s'agit d'un attribut de l'entité
			$field->setValue( $this->entity->$field_name() ); // On assigne la valeur correspondante au champs
		}
		elseif (isset($_POST[$field_name])) {
			$field->setValue($_POST[$field_name]); // On assigne la valeur correspondante au champ.
		}

		return $this;
}
	
	/**
	 * Génère le code associé au formulaire objet.
	 *
	 * @return string
	 */
	public function createView() {
		$view = '';
		// Construire tous les fields
		foreach ( $this->Field_a as $field ) {
			$view .= $field->buildWidget();
			$view .= '<br />';
		}
		
		return $view;
	}
	
	/**
	 * Vérifie si le formulaire est valide ou pas.
	 * Un formulaire est valide si tous ses champs sont valides.
	 *
	 * @return bool
	 */
	public function isValid() {
		$valid = true;
		foreach ( $this->Field_a as $Field ) {
			//var_dump($Field);
			if ( !$Field->isValid() ) {
				$valid = false;
			}
		}
		return $valid;
	}
	
	/**
	 * @return Entity
	 */
	public function entity() {
		return $this->entity;
	}
	
	/**
	 * @return Field[]
	 */
	public function Field_a() {
		return $this->Field_a;
	}
	
	/**
	 * Setter de l'attribut $entity.
	 *
	 * @param Entity $entity
	 */
	public function setEntity( Entity $entity ) {
		$this->entity = $entity;
	}
	
	/**
	 * Recherche un champ de formulaire par son nom
	 *
	 * @param string $field_name
	 *
	 * @return Field|null
	 */
	public function getFieldFromName($field_name) {
		foreach ($this->Field_a as $Field) {
			if ($Field->name() === $field_name) {
				return $Field;
			}
		}
		return null;
	}
}