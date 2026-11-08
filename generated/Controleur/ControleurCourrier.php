<?php
/**
 * Class that operate on table 'courrier'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurCourrier extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $courrier = new Courrier();
$courrier->iDCOU=$this->requete->getParametre("iDCOU");
$courrier->oBJ=$this->requete->getParametre("oBJ");
$courrier->dATENR=$this->requete->getParametre("dATENR");
$courrier->nBRP=$this->requete->getParametre("nBRP");
$courrier->eXP=$this->requete->getParametre("eXP");
$courrier->oBS=$this->requete->getParametre("oBS");
$courrier->chemin=$this->requete->getParametre("chemin");
DAOFactory::getCourrierDAO()->insert($courrier);

		$this->rediriger("courrier/ajout");

	}


}
?>
