<?php
namespace OCFram;

class PDOFactory
{
	public static function getPDO()
	{
		$db = new \PDO('mysql:host=localhost;dbname=news;charset=utf8', 'root', 'root');
		$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		
		return $db;
	}
}