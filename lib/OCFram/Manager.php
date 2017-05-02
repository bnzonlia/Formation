<?php

namespace OCFram;

/**
 * Class Manager
 * Classe représentant un manager type pour un module.
 *
 * @package OCFram
 */
abstract class Manager {
	/**
	 * @var $dao \PDO Data Access Object for DB queries
	 */
	protected $dao;
	
	/**
	 * Manager constructor.
	 *
	 * @param $dao mixed
	 */
	public function __construct( $dao ) {
		$this->dao = $dao;
	}
	
}