<?php
/**
 * Class that operate on table 'entrant'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class EntrantMySqlDAO implements EntrantDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return EntrantMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM entrant WHERE ID_ENT = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM entrant';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM entrant ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param entrant primary key
 	 */
	public function delete($ID_ENT){
		$sql = 'DELETE FROM entrant WHERE ID_ENT = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_ENT);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param EntrantMySql entrant
 	 */
	public function insert($entrant){
		$sql = 'INSERT INTO entrant (ID_COU, DAT_COU, num_ord) VALUES (?, ?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($entrant->iDCOU);
		$sqlQuery->set($entrant->dATCOU);
		$sqlQuery->set($entrant->numOrd);

		$id = $this->executeInsert($sqlQuery);	
		$entrant->iDENT = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param EntrantMySql entrant
 	 */
	public function update($entrant){
		$sql = 'UPDATE entrant SET ID_COU = ?, DAT_COU = ?, num_ord = ? WHERE ID_ENT = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($entrant->iDCOU);
		$sqlQuery->set($entrant->dATCOU);
		$sqlQuery->set($entrant->numOrd);

		$sqlQuery->setNumber($entrant->iDENT);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM entrant';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByIDCOU($value){
		$sql = 'SELECT * FROM entrant WHERE ID_COU = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByDATCOU($value){
		$sql = 'SELECT * FROM entrant WHERE DAT_COU = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByNumOrd($value){
		$sql = 'SELECT * FROM entrant WHERE num_ord = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByIDCOU($value){
		$sql = 'DELETE FROM entrant WHERE ID_COU = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByDATCOU($value){
		$sql = 'DELETE FROM entrant WHERE DAT_COU = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByNumOrd($value){
		$sql = 'DELETE FROM entrant WHERE num_ord = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return EntrantMySql 
	 */
	protected function readRow($row){
		$entrant = new Entrant();
		
		$entrant->iDENT = $row['ID_ENT'];
		$entrant->iDCOU = $row['ID_COU'];
		$entrant->dATCOU = $row['DAT_COU'];
		$entrant->numOrd = $row['num_ord'];

		return $entrant;
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
	 * @return EntrantMySql 
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