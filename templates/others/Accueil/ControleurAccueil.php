<?php

class ControleurAccueil extends Controleur{


  public function __construct() {
  

  }
      
    public function index() {
	
    $this->genererVue(array('ras'=> ''), null, 'accueil');
		
  }
  
}

?>