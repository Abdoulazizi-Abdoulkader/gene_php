<?php
/**
 * Class that operate on table '${table_name}'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: ${date}
 */

class Controleur${domain_class_name} extends Controleur{

	private $${table_name}Manager ;

	public function __construct() {
		$this->${table_name}Manager = ManagersFactory::get${domain_class_name}Manager();
	}

	public function index() {
		$this->genererVue(array('donnee'=> $this->${table_name}Manager->queryAll()), null, '${table_name}');
	}

	public function ajout() {
		$this->genererVue(array('ras'=> ''), null, '${table_name}');
	}

	public function modification() {
		$var = $this->${table_name}Manager->load($this->requete->getParametre('id'));
		if($this->requete->getParametre("btn")=="btn_edit")
			$this->genererVue(array('${table_name}'=> $var), null, "${table_name}");
		else
			$this->genererVue(array('${table_name}'=> $var, 'id'=> $this->requete->getParametre('id')),"details","${table_name}");
	}
	
	public function enregistrer() {
        ${content}
		$${table_name} = new ${domain_class_name}($data);
		$this->${table_name}Manager->insert($${table_name});
		$this->genererVue(array('msgErreur' => 'Enregistrement réussi avec succès','erreurType'=>'info'), "ajout", "${table_name}");
	}
	
	public function modifier() {
        ${content}
		$${table_name} = new ${domain_class_name}($data);
		$this->${table_name}Manager->update($${table_name});
		$this->genererVue(array('msgErreur' => 'Mise à jour effectuée avec succès.','erreurType'=>'info','btn'=>'btn_view','id'=>''.$${table_name}->get${pk}(), '${table_name}'=>$${table_name}), "details","${table_name}");

	}

}
?>
