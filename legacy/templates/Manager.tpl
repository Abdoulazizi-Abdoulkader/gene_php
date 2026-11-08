<?php
/**
 * Class that operate on table '${table_name}'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: ${date}
 */
class ${dao_clazz_name}Manager extends Modele{

	protected $bdd ;

	public function __construct(){
		$this->bdd = $this->getBdd();
	} 

	/**
	 * Get object by primary key
	 *
	 * @param String $id primary key
	 * @return ${dao_clazz_name} 
	 */
	public function load($id){
	
		/*$q = $this->bdd->query('SELECT * FROM ${table_name} WHERE ${pk} = '.$id);
		return $this->readRow($q->fetch(\PDO::FETCH_ASSOC)); */
		
		$q = $this->bdd->prepare('SELECT * FROM ${table_name} WHERE ${pk} = :id');
		$q->bindValue(':id', $id);
		$q->execute();
		return $this->readRow($q->fetch(\PDO::FETCH_ASSOC));
		
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$req = 'SELECT * FROM ${table_name}';
		$q = $this->bdd->query($req);
		$liste = array();
		while($data = $q->fetch(PDO::FETCH_ASSOC)){
		$liste[] = $this->readRow($data);
		}
		return $liste;
	}
	
	/**
	 * Get all records from table by query
	 */
	public function queryAllByQuery($req){
		$q = $this->bdd->query($req);
		$liste = array();
		while($data = $q->fetch(PDO::FETCH_ASSOC)){
		$liste[] = $this->readRow($data);
		}
		return $liste;
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$req = 'SELECT * FROM ${table_name} ORDER BY '.$orderColumn;
		$q = $this->bdd->query($req);
		$liste = array();
		while($data = $q->fetch(PDO::FETCH_ASSOC)){
		$liste[] = $this->readRow($data);
		}
		return $liste;
	}
	
	/**
 	 * Insert record to table
 	 *
 	 *
 	 */
	public function insert($${table_name}){

		$q = $this->bdd->prepare('INSERT INTO ${table_name} (${insert_fields}) VALUES(${insertFieldsBis})');
		
${bindValue}
		$q->execute();  
		
	}
	
	/**
 	 * Update record in table
 	 *
 	 * 
 	 */
	public function update($${table_name}){
		
		$q = $this->bdd->prepare('UPDATE ${table_name} SET ${update_fields} WHERE ${pk} = :${pk}');
		
${bindValue}
		$q->bindValue(':${pk}',$${table_name}->get${bindPk}());
		$q->execute();  
		
	}
	
		/**
	 * Read row
	 *
	 * @return ${dao_clazz_name} 
	 */
	protected function readRow($row){
		${read_row}
		$${table_name} = new ${dao_clazz_name}($data);
		return $${table_name};
	}
	
	protected function getList($tab){
		$ret = array();
		for($i=0;$i<count($tab);$i++){
			$ret[$i] = $this->readRow($tab[$i]);
		}
		return $ret;
	}

}
?>