<?php
/**
 * Class that operate on table 'caractere'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class CaractereMySqlDAO implements CaractereDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return CaractereMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM caractere WHERE ID_CAR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM caractere';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM caractere ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param caractere primary key
 	 */
	public function delete($ID_CAR){
		$sql = 'DELETE FROM caractere WHERE ID_CAR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_CAR);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param CaractereMySql caractere
 	 */
	public function insert($caractere){
		$sql = 'INSERT INTO caractere (LIB_CAR) VALUES (?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($caractere->lIBCAR);

		$id = $this->executeInsert($sqlQuery);	
		$caractere->iDCAR = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param CaractereMySql caractere
 	 */
	public function update($caractere){
		$sql = 'UPDATE caractere SET LIB_CAR = ? WHERE ID_CAR = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($caractere->lIBCAR);

		$sqlQuery->setNumber($caractere->iDCAR);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM caractere';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByLIBCAR($value){
		$sql = 'SELECT * FROM caractere WHERE LIB_CAR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByLIBCAR($value){
		$sql = 'DELETE FROM caractere WHERE LIB_CAR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return CaractereMySql 
	 */
	protected function readRow($row){
		$caractere = new Caractere();
		
		$caractere->iDCAR = $row['ID_CAR'];
		$caractere->lIBCAR = $row['LIB_CAR'];

		return $caractere;
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
	 * @return CaractereMySql 
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