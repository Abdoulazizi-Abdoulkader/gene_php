<?php
/**
 * Class that operate on table 'sortant'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurSortant extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $sortant = new Sortant();
$sortant->iDSOR=$this->requete->getParametre("iDSOR");
$sortant->iDCOU=$this->requete->getParametre("iDCOU");
$sortant->dES=$this->requete->getParametre("dES");
DAOFactory::getSortantDAO()->insert($sortant);

		$this->rediriger("sortant/ajout");

	}


}
?>
