<?php
/**
 * Class that operate on table 'sortant'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class SortantMySqlDAO implements SortantDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return SortantMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM sortant WHERE ID_SOR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM sortant';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM sortant ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param sortant primary key
 	 */
	public function delete($ID_SOR){
		$sql = 'DELETE FROM sortant WHERE ID_SOR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_SOR);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param SortantMySql sortant
 	 */
	public function insert($sortant){
		$sql = 'INSERT INTO sortant (ID_COU, DES) VALUES (?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($sortant->iDCOU);
		$sqlQuery->set($sortant->dES);

		$id = $this->executeInsert($sqlQuery);	
		$sortant->iDSOR = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param SortantMySql sortant
 	 */
	public function update($sortant){
		$sql = 'UPDATE sortant SET ID_COU = ?, DES = ? WHERE ID_SOR = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($sortant->iDCOU);
		$sqlQuery->set($sortant->dES);

		$sqlQuery->setNumber($sortant->iDSOR);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM sortant';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByIDCOU($value){
		$sql = 'SELECT * FROM sortant WHERE ID_COU = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByDES($value){
		$sql = 'SELECT * FROM sortant WHERE DES = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByIDCOU($value){
		$sql = 'DELETE FROM sortant WHERE ID_COU = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByDES($value){
		$sql = 'DELETE FROM sortant WHERE DES = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return SortantMySql 
	 */
	protected function readRow($row){
		$sortant = new Sortant();
		
		$sortant->iDSOR = $row['ID_SOR'];
		$sortant->iDCOU = $row['ID_COU'];
		$sortant->dES = $row['DES'];

		return $sortant;
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
	 * @return SortantMySql 
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