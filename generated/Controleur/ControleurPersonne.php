<?php
/**
 * Class that operate on table 'personne'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurPersonne extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $personne = new Personne();
$personne->iDPER=$this->requete->getParametre("iDPER");
$personne->iDSER=$this->requete->getParametre("iDSER");
$personne->nOMPER=$this->requete->getParametre("nOMPER");
$personne->pRENOM=$this->requete->getParametre("pRENOM");
$personne->aGE=$this->requete->getParametre("aGE");
$personne->tEL=$this->requete->getParametre("tEL");
$personne->mAIL=$this->requete->getParametre("mAIL");
DAOFactory::getPersonneDAO()->insert($personne);

		$this->rediriger("personne/ajout");

	}


}
?>
