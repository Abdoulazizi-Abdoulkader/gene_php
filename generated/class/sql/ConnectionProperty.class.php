<?php
/**
 * Connection properties
 *
 * @author: http://phpdao.com
 * @date: 27.11.2007
 */
class ConnectionProperty{
	private static $host = Configuration::get("host");
	private static $user = Configuration::get("login");
	private static $password = Configuration::get("mdp");
	private static $database = Configuration::get("database");

	public static function getHost(){
		return ConnectionProperty::$host;
	}

	public static function getUser(){
		return ConnectionProperty::$user;
	}

	public static function getPassword(){
		return ConnectionProperty::$password;
	}

	public static function getDatabase(){
		return ConnectionProperty::$database;
	}
}
?>