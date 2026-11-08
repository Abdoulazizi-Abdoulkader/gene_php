<?php
/**
 * Class that operate on table 'concerne'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurConcerne extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $concerne = new Concerne();
$concerne->iDDIR=$this->requete->getParametre("iDDIR");
$concerne->iDCOU=$this->requete->getParametre("iDCOU");
$concerne->iDCON=$this->requete->getParametre("iDCON");
$concerne->archive=$this->requete->getParametre("archive");
$concerne->traite=$this->requete->getParametre("traite");
DAOFactory::getConcerneDAO()->insert($concerne);

		$this->rediriger("concerne/ajout");

	}


}
?>
