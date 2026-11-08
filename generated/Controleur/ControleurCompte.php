<?php
/**
 * Class that operate on table 'compte'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurCompte extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $compte = new Compte();
$compte->iDUSE=$this->requete->getParametre("iDUSE");
$compte->iDPER=$this->requete->getParametre("iDPER");
$compte->lOGIN=$this->requete->getParametre("lOGIN");
$compte->pWD=$this->requete->getParametre("pWD");
$compte->iDPRO=$this->requete->getParametre("iDPRO");
DAOFactory::getCompteDAO()->insert($compte);

		$this->rediriger("compte/ajout");

	}


}
?>
