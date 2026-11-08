<?php
/**
 * Class that operate on table 'personne'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class PersonneMySqlDAO implements PersonneDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return PersonneMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM personne WHERE ID_PER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM personne';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM personne ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param personne primary key
 	 */
	public function delete($ID_PER){
		$sql = 'DELETE FROM personne WHERE ID_PER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_PER);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param PersonneMySql personne
 	 */
	public function insert($personne){
		$sql = 'INSERT INTO personne (ID_SER, NOM_PER, PRENOM, AGE, TEL, MAIL) VALUES (?, ?, ?, ?, ?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($personne->iDSER);
		$sqlQuery->set($personne->nOMPER);
		$sqlQuery->set($personne->pRENOM);
		$sqlQuery->setNumber($personne->aGE);
		$sqlQuery->set($personne->tEL);
		$sqlQuery->set($personne->mAIL);

		$id = $this->executeInsert($sqlQuery);	
		$personne->iDPER = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param PersonneMySql personne
 	 */
	public function update($personne){
		$sql = 'UPDATE personne SET ID_SER = ?, NOM_PER = ?, PRENOM = ?, AGE = ?, TEL = ?, MAIL = ? WHERE ID_PER = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($personne->iDSER);
		$sqlQuery->set($personne->nOMPER);
		$sqlQuery->set($personne->pRENOM);
		$sqlQuery->setNumber($personne->aGE);
		$sqlQuery->set($personne->tEL);
		$sqlQuery->set($personne->mAIL);

		$sqlQuery->setNumber($personne->iDPER);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM personne';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByIDSER($value){
		$sql = 'SELECT * FROM personne WHERE ID_SER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByNOMPER($value){
		$sql = 'SELECT * FROM personne WHERE NOM_PER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByPRENOM($value){
		$sql = 'SELECT * FROM personne WHERE PRENOM = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByAGE($value){
		$sql = 'SELECT * FROM personne WHERE AGE = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByTEL($value){
		$sql = 'SELECT * FROM personne WHERE TEL = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByMAIL($value){
		$sql = 'SELECT * FROM personne WHERE MAIL = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByIDSER($value){
		$sql = 'DELETE FROM personne WHERE ID_SER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByNOMPER($value){
		$sql = 'DELETE FROM personne WHERE NOM_PER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByPRENOM($value){
		$sql = 'DELETE FROM personne WHERE PRENOM = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByAGE($value){
		$sql = 'DELETE FROM personne WHERE AGE = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByTEL($value){
		$sql = 'DELETE FROM personne WHERE TEL = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByMAIL($value){
		$sql = 'DELETE FROM personne WHERE MAIL = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return PersonneMySql 
	 */
	protected function readRow($row){
		$personne = new Personne();
		
		$personne->iDPER = $row['ID_PER'];
		$personne->iDSER = $row['ID_SER'];
		$personne->nOMPER = $row['NOM_PER'];
		$personne->pRENOM = $row['PRENOM'];
		$personne->aGE = $row['AGE'];
		$personne->tEL = $row['TEL'];
		$personne->mAIL = $row['MAIL'];

		return $personne;
	}
	
	protected function getList($sqlQuery){
		$tab = QueryExecutor::execute($sqlQuery);
		$ret = array();
		for($i=0;$i<count($tab);$i++){
			$ret[$i] = $this->readRow($tab[$i]);
		}
		return $ret;
	}
	
	/**
	 * Get row
	 *
	 * @return PersonneMySql 
	 */
	protected function getRow($sqlQuery){
		$tab = QueryExecutor::execute($sqlQuery);
		if(count($tab)==0){
			return null;
		}
		return $this->readRow($tab[0]);		
	}
	
	/**
	 * Execute sql query
	 */
	protected function execute($sqlQuery){
		return QueryExecutor::execute($sqlQuery);
	}
	
		
	/**
	 * Execute sql query
	 */
	protected function executeUpdate($sqlQuery){
		return QueryExecutor::executeUpdate($sqlQuery);
	}

	/**
	 * Query for one row and one column
	 */
	protected function querySingleResult($sqlQuery){
		return QueryExecutor::queryForString($sqlQuery);
	}

	/**
	 * Insert row to table
	 */
	protected function executeInsert($sqlQuery){
		return QueryExecutor::executeInsert($sqlQuery);
	}
}
?>