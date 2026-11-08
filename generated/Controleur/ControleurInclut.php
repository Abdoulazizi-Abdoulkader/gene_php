<?php
/**
 * Class that operate on table 'inclut'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurInclut extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $inclut = new Inclut();
$inclut->iDFIC=$this->requete->getParametre("iDFIC");
$inclut->iDINS=$this->requete->getParametre("iDINS");
$inclut->iDINC=$this->requete->getParametre("iDINC");
DAOFactory::getInclutDAO()->insert($inclut);

		$this->rediriger("inclut/ajout");

	}


}
?>
