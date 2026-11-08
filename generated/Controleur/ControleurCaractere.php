<?php
/**
 * Class that operate on table 'caractere'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurCaractere extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $caractere = new Caractere();
$caractere->iDCAR=$this->requete->getParametre("iDCAR");
$caractere->lIBCAR=$this->requete->getParametre("lIBCAR");
DAOFactory::getCaractereDAO()->insert($caractere);

		$this->rediriger("caractere/ajout");

	}


}
?>
