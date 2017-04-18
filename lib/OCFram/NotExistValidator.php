<?php
namespace OCFram;
class NotExistValidator extends Validator
{
	protected $manager;
	protected $functionname;
	
	public function __construct( $errorMessage, Manager $manager, $function) {
		
		parent::__construct( $errorMessage );
		
		$this->setManager($manager);
		$this->setFunctionname($function);
	}
	
	public function isValid($value)
	{

		$user =$this->manager->getUserWithoutGuestUsingLogin($value);
		if($user==null)
		{
			return true;
		}
         
		return false;
	}
	
	public function setManager($manager)
	{
		$this->manager = $manager;
	}
	
	public function setFunctionname($functionname)
	{
		$this->functionname= $functionname;
	}
}