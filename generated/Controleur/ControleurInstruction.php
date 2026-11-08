<?php
/**
 * Class that operate on table 'instruction'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

class ControleurInstruction extends Controleur{

	public function __construct() {

	}

	public function index() {
	$this->genererVue();
	}

	public function ajout() {
	$this->genererVue();
	}
	
	public function enregistrer() {
        $instruction = new Instruction();
$instruction->iDINS=$this->requete->getParametre("iDINS");
$instruction->lIBINS=$this->requete->getParametre("lIBINS");
DAOFactory::getInstructionDAO()->insert($instruction);

		$this->rediriger("instruction/ajout");

	}


}
?>
