<?php
/**
 * Class that operate on table 'concerne'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class ConcerneMySqlDAO implements ConcerneDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return ConcerneMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM concerne WHERE ID_CON = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM concerne';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM concerne ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param concerne primary key
 	 */
	public function delete($ID_CON){
		$sql = 'DELETE FROM concerne WHERE ID_CON = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_CON);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param ConcerneMySql concerne
 	 */
	public function insert($concerne){
		$sql = 'INSERT INTO concerne (ID_DIR, ID_COU, archive, traite) VALUES (?, ?, ?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($concerne->iDDIR);
		$sqlQuery->setNumber($concerne->iDCOU);
		$sqlQuery->setNumber($concerne->archive);
		$sqlQuery->setNumber($concerne->traite);

		$id = $this->executeInsert($sqlQuery);	
		$concerne->iDCON = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param ConcerneMySql concerne
 	 */
	public function update($concerne){
		$sql = 'UPDATE concerne SET ID_DIR = ?, ID_COU = ?, archive = ?, traite = ? WHERE ID_CON = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($concerne->iDDIR);
		$sqlQuery->setNumber($concerne->iDCOU);
		$sqlQuery->setNumber($concerne->archive);
		$sqlQuery->setNumber($concerne->traite);

		$sqlQuery->setNumber($concerne->iDCON);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM concerne';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByIDDIR($value){
		$sql = 'SELECT * FROM concerne WHERE ID_DIR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByIDCOU($value){
		$sql = 'SELECT * FROM concerne WHERE ID_COU = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByArchive($value){
		$sql = 'SELECT * FROM concerne WHERE archive = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByTraite($value){
		$sql = 'SELECT * FROM concerne WHERE traite = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByIDDIR($value){
		$sql = 'DELETE FROM concerne WHERE ID_DIR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByIDCOU($value){
		$sql = 'DELETE FROM concerne WHERE ID_COU = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByArchive($value){
		$sql = 'DELETE FROM concerne WHERE archive = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByTraite($value){
		$sql = 'DELETE FROM concerne WHERE traite = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return ConcerneMySql 
	 */
	protected function readRow($row){
		$concerne = new Concerne();
		
		$concerne->iDDIR = $row['ID_DIR'];
		$concerne->iDCOU = $row['ID_COU'];
		$concerne->iDCON = $row['ID_CON'];
		$concerne->archive = $row['archive'];
		$concerne->traite = $row['traite'];

		return $concerne;
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
	 * @return ConcerneMySql 
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