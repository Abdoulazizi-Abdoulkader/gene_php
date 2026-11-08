<?php
/**
 * Class that operate on table 'fiche'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurFiche extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $fiche = new Fiche();
$fiche->iDFIC=$this->requete->getParametre("iDFIC");
$fiche->iDCAR=$this->requete->getParametre("iDCAR");
$fiche->iDCON=$this->requete->getParametre("iDCON");
$fiche->obser=$this->requete->getParametre("obser");
DAOFactory::getFicheDAO()->insert($fiche);

		$this->rediriger("fiche/ajout");

	}


}
?>
