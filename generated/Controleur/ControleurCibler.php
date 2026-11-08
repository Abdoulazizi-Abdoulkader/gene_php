<?php
/**
 * Class that operate on table 'cibler'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurCibler extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $cibler = new Cibler();
$cibler->iDSER=$this->requete->getParametre("iDSER");
$cibler->iDFIC=$this->requete->getParametre("iDFIC");
$cibler->iDCIB=$this->requete->getParametre("iDCIB");
$cibler->archive=$this->requete->getParametre("archive");
$cibler->vue=$this->requete->getParametre("vue");
DAOFactory::getCiblerDAO()->insert($cibler);

		$this->rediriger("cibler/ajout");

	}


}
?>
