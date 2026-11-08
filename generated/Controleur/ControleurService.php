<?php
/**
 * Class that operate on table 'service'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurService extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $service = new Service();
$service->iDSER=$this->requete->getParametre("iDSER");
$service->iDDIR=$this->requete->getParametre("iDDIR");
$service->nOMSER=$this->requete->getParametre("nOMSER");
DAOFactory::getServiceDAO()->insert($service);

		$this->rediriger("service/ajout");

	}


}
?>
