<?php
/**
 * Class that operate on table 'profile'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurProfile extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $profile = new Profile();
$profile->iDPRO=$this->requete->getParametre("iDPRO");
$profile->nOMPRO=$this->requete->getParametre("nOMPRO");
DAOFactory::getProfileDAO()->insert($profile);

		$this->rediriger("profile/ajout");

	}


}
?>
