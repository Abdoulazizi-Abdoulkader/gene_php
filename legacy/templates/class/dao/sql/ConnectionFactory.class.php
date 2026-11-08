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

	 public function __construct()
	 {
		
	 }
	static public function getConnection(){
		$conn = mysqli_connect(Configuration::get("host"), Configuration::get("login"), Configuration::get("mdp"),Configuration::get("database"));
		//mysql_select_db(Configuration::get("database"));
		/*if(!$conn){
			throw new Exception('could not connect to database');
		}*/

		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
			exit();
		  }
		return $conn;
	}

	/**
	 * Zamkniecie polaczenia
	 *
	 * @param connection polaczenie do bazy
	 */
	static public function close($connection){
		mysqli_close($connection);
	}
}
?>