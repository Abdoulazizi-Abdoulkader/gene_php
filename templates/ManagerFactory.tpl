<?php

/**
 * ManagersFactory
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: ${date}
 */
class ManagersFactory{
	${content}
	
	public static function getResultRowByQuery($query){
	
	// Récupération des paramètres de configuration BD
	$dsn = Configuration::get("dsn");
	$login = Configuration::get("login");
	$mdp = Configuration::get("mdp");
	// Création de la connexion
	$bdd = new PDO($dsn, $login, $mdp,
	array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	
	$q = $bdd->query($req);
	$liste = array();
	while($data = $q->fetch(PDO::FETCH_ASSOC))
	$liste[] = $data;
	return $liste;

	}
}
?>