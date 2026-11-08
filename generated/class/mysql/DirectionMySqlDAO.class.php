<?php
/**
 * Class that operate on table 'direction'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class DirectionMySqlDAO implements DirectionDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return DirectionMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM direction WHERE ID_DIR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM direction';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM direction ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param direction primary key
 	 */
	public function delete($ID_DIR){
		$sql = 'DELETE FROM direction WHERE ID_DIR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_DIR);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param DirectionMySql direction
 	 */
	public function insert($direction){
		$sql = 'INSERT INTO direction (NOM_DIR) VALUES (?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($direction->nOMDIR);

		$id = $this->executeInsert($sqlQuery);	
		$direction->iDDIR = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param DirectionMySql direction
 	 */
	public function update($direction){
		$sql = 'UPDATE direction SET NOM_DIR = ? WHERE ID_DIR = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($direction->nOMDIR);

		$sqlQuery->setNumber($direction->iDDIR);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM direction';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByNOMDIR($value){
		$sql = 'SELECT * FROM direction WHERE NOM_DIR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByNOMDIR($value){
		$sql = 'DELETE FROM direction WHERE NOM_DIR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return DirectionMySql 
	 */
	protected function readRow($row){
		$direction = new Direction();
		
		$direction->iDDIR = $row['ID_DIR'];
		$direction->nOMDIR = $row['NOM_DIR'];

		return $direction;
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
	 * @return DirectionMySql 
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