<?php
/**
 * Class that operate on table 'entrant'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurEntrant extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $entrant = new Entrant();
$entrant->iDENT=$this->requete->getParametre("iDENT");
$entrant->iDCOU=$this->requete->getParametre("iDCOU");
$entrant->dATCOU=$this->requete->getParametre("dATCOU");
$entrant->numOrd=$this->requete->getParametre("numOrd");
DAOFactory::getEntrantDAO()->insert($entrant);

		$this->rediriger("entrant/ajout");

	}


}
?>
