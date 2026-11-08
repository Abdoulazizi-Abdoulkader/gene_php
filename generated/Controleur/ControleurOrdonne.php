<?php
/**
 * Class that operate on table 'ordonne'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurOrdonne extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $ordonne = new Ordonne();
$ordonne->iDDIR=$this->requete->getParametre("iDDIR");
$ordonne->iDINS=$this->requete->getParametre("iDINS");
$ordonne->iDORD=$this->requete->getParametre("iDORD");
DAOFactory::getOrdonneDAO()->insert($ordonne);

		$this->rediriger("ordonne/ajout");

	}


}
?>
