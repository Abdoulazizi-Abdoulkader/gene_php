<?php
/**
 * Class that operate on table 'compte'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class CompteMySqlDAO implements CompteDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return CompteMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM compte WHERE ID_USE = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM compte';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM compte ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param compte primary key
 	 */
	public function delete($ID_USE){
		$sql = 'DELETE FROM compte WHERE ID_USE = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_USE);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param CompteMySql compte
 	 */
	public function insert($compte){
		$sql = 'INSERT INTO compte (ID_PER, LOGIN, PWD, ID_PRO) VALUES (?, ?, ?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($compte->iDPER);
		$sqlQuery->set($compte->lOGIN);
		$sqlQuery->set($compte->pWD);
		$sqlQuery->set($compte->iDPRO);

		$id = $this->executeInsert($sqlQuery);	
		$compte->iDUSE = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param CompteMySql compte
 	 */
	public function update($compte){
		$sql = 'UPDATE compte SET ID_PER = ?, LOGIN = ?, PWD = ?, ID_PRO = ? WHERE ID_USE = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($compte->iDPER);
		$sqlQuery->set($compte->lOGIN);
		$sqlQuery->set($compte->pWD);
		$sqlQuery->set($compte->iDPRO);

		$sqlQuery->setNumber($compte->iDUSE);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM compte';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByIDPER($value){
		$sql = 'SELECT * FROM compte WHERE ID_PER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByLOGIN($value){
		$sql = 'SELECT * FROM compte WHERE LOGIN = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByPWD($value){
		$sql = 'SELECT * FROM compte WHERE PWD = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByIDPRO($value){
		$sql = 'SELECT * FROM compte WHERE ID_PRO = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByIDPER($value){
		$sql = 'DELETE FROM compte WHERE ID_PER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByLOGIN($value){
		$sql = 'DELETE FROM compte WHERE LOGIN = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByPWD($value){
		$sql = 'DELETE FROM compte WHERE PWD = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByIDPRO($value){
		$sql = 'DELETE FROM compte WHERE ID_PRO = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return CompteMySql 
	 */
	protected function readRow($row){
		$compte = new Compte();
		
		$compte->iDUSE = $row['ID_USE'];
		$compte->iDPER = $row['ID_PER'];
		$compte->lOGIN = $row['LOGIN'];
		$compte->pWD = $row['PWD'];
		$compte->iDPRO = $row['ID_PRO'];

		return $compte;
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
	 * @return CompteMySql 
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