<?php
	/**
	 * Object represents table '${table_name}'
	 *
		 * @author: Mahaman Sani NOMAOU DAN LAMSO
     	 * @date: ${date}	 
	 */
	class ${domain_class_name}{
		${variables}		
		 public function __construct($data){
			$this->hydrate($data);
		} 
		
		public function hydrate($data){
			foreach($data AS $key=>$value){
				$method = 'set'.ucfirst($key);
				if(method_exists($this,$method)){
					$this->$method ($value); 
				}  
			}
		} 
		${getter_setter}		
	}
?>