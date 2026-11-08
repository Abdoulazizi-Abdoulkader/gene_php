<?php
/**
 * Class that operate on table 'direction'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurDirection extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $direction = new Direction();
$direction->iDDIR=$this->requete->getParametre("iDDIR");
$direction->nOMDIR=$this->requete->getParametre("nOMDIR");
DAOFactory::getDirectionDAO()->insert($direction);

		$this->rediriger("direction/ajout");

	}


}
?>
