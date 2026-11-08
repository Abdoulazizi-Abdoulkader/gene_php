<?php
/**
 * Class that operate on table 'repond'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class RepondMySqlDAO implements RepondDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return RepondMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM repond WHERE ID_REP = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM repond';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM repond ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param repond primary key
 	 */
	public function delete($ID_REP){
		$sql = 'DELETE FROM repond WHERE ID_REP = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_REP);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param RepondMySql repond
 	 */
	public function insert($repond){
		$sql = 'INSERT INTO repond (ID_SOR, ID_ENT) VALUES (?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($repond->iDSOR);
		$sqlQuery->setNumber($repond->iDENT);

		$id = $this->executeInsert($sqlQuery);	
		$repond->iDREP = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param RepondMySql repond
 	 */
	public function update($repond){
		$sql = 'UPDATE repond SET ID_SOR = ?, ID_ENT = ? WHERE ID_REP = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($repond->iDSOR);
		$sqlQuery->setNumber($repond->iDENT);

		$sqlQuery->setNumber($repond->iDREP);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM repond';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByIDSOR($value){
		$sql = 'SELECT * FROM repond WHERE ID_SOR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByIDENT($value){
		$sql = 'SELECT * FROM repond WHERE ID_ENT = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByIDSOR($value){
		$sql = 'DELETE FROM repond WHERE ID_SOR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByIDENT($value){
		$sql = 'DELETE FROM repond WHERE ID_ENT = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return RepondMySql 
	 */
	protected function readRow($row){
		$repond = new Repond();
		
		$repond->iDSOR = $row['ID_SOR'];
		$repond->iDENT = $row['ID_ENT'];
		$repond->iDREP = $row['ID_REP'];

		return $repond;
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
	 * @return RepondMySql 
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