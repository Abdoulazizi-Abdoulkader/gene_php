<?php
/**
 * Class that operate on table 'repond'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurRepond extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $repond = new Repond();
$repond->iDSOR=$this->requete->getParametre("iDSOR");
$repond->iDENT=$this->requete->getParametre("iDENT");
$repond->iDREP=$this->requete->getParametre("iDREP");
DAOFactory::getRepondDAO()->insert($repond);

		$this->rediriger("repond/ajout");

	}


}
?>
