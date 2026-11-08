<?php
/**
 * Class that operate on table 'ordonne'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class OrdonneMySqlDAO implements OrdonneDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return OrdonneMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM ordonne WHERE ID_ORD = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM ordonne';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM ordonne ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param ordonne primary key
 	 */
	public function delete($ID_ORD){
		$sql = 'DELETE FROM ordonne WHERE ID_ORD = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_ORD);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param OrdonneMySql ordonne
 	 */
	public function insert($ordonne){
		$sql = 'INSERT INTO ordonne (ID_DIR, ID_INS) VALUES (?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($ordonne->iDDIR);
		$sqlQuery->setNumber($ordonne->iDINS);

		$id = $this->executeInsert($sqlQuery);	
		$ordonne->iDORD = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param OrdonneMySql ordonne
 	 */
	public function update($ordonne){
		$sql = 'UPDATE ordonne SET ID_DIR = ?, ID_INS = ? WHERE ID_ORD = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($ordonne->iDDIR);
		$sqlQuery->setNumber($ordonne->iDINS);

		$sqlQuery->setNumber($ordonne->iDORD);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM ordonne';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByIDDIR($value){
		$sql = 'SELECT * FROM ordonne WHERE ID_DIR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByIDINS($value){
		$sql = 'SELECT * FROM ordonne WHERE ID_INS = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByIDDIR($value){
		$sql = 'DELETE FROM ordonne WHERE ID_DIR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByIDINS($value){
		$sql = 'DELETE FROM ordonne WHERE ID_INS = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return OrdonneMySql 
	 */
	protected function readRow($row){
		$ordonne = new Ordonne();
		
		$ordonne->iDDIR = $row['ID_DIR'];
		$ordonne->iDINS = $row['ID_INS'];
		$ordonne->iDORD = $row['ID_ORD'];

		return $ordonne;
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
	 * @return OrdonneMySql 
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