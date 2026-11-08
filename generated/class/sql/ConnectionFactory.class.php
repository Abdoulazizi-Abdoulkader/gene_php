<?php
/*
 * Class return connection to database
 *
 * @author: http://phpdao.com
 * @date: 27.11.2007
 */
class ConnectionFactory{
	
	/**
	 * Zwrocenie polaczenia
	 *
	 * @return polaczenie
	 */
	static public function getConnection(){
		$conn = mysql_connect(Configuration::get("host"), Configuration::get("login"), Configuration::get("mdp"));
		mysql_select_db(Configuration::get("database"));
		if(!$conn){
			throw new Exception('could not connect to database');
		}
		return $conn;
	}

	/**
	 * Zamkniecie polaczenia
	 *
	 * @param connection polaczenie do bazy
	 */
	static public function close($connection){
		mysql_close($connection);
	}
}
?>