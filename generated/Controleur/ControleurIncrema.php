<?php
/**
 * Class that operate on table 'increma'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurIncrema extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $increma = new Increma();
$increma->id=$this->requete->getParametre("id");
DAOFactory::getIncremaDAO()->insert($increma);

		$this->rediriger("increma/ajout");

	}


}
?>
