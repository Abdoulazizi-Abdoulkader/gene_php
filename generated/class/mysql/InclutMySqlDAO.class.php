<?php
/**
 * Class that operate on table 'inclut'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class InclutMySqlDAO implements InclutDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return InclutMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM inclut WHERE ID_INC = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM inclut';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM inclut ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param inclut primary key
 	 */
	public function delete($ID_INC){
		$sql = 'DELETE FROM inclut WHERE ID_INC = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_INC);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param InclutMySql inclut
 	 */
	public function insert($inclut){
		$sql = 'INSERT INTO inclut (ID_FIC, ID_INS) VALUES (?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($inclut->iDFIC);
		$sqlQuery->setNumber($inclut->iDINS);

		$id = $this->executeInsert($sqlQuery);	
		$inclut->iDINC = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param InclutMySql inclut
 	 */
	public function update($inclut){
		$sql = 'UPDATE inclut SET ID_FIC = ?, ID_INS = ? WHERE ID_INC = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($inclut->iDFIC);
		$sqlQuery->setNumber($inclut->iDINS);

		$sqlQuery->setNumber($inclut->iDINC);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM inclut';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByIDFIC($value){
		$sql = 'SELECT * FROM inclut WHERE ID_FIC = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByIDINS($value){
		$sql = 'SELECT * FROM inclut WHERE ID_INS = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByIDFIC($value){
		$sql = 'DELETE FROM inclut WHERE ID_FIC = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByIDINS($value){
		$sql = 'DELETE FROM inclut WHERE ID_INS = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return InclutMySql 
	 */
	protected function readRow($row){
		$inclut = new Inclut();
		
		$inclut->iDFIC = $row['ID_FIC'];
		$inclut->iDINS = $row['ID_INS'];
		$inclut->iDINC = $row['ID_INC'];

		return $inclut;
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
	 * @return InclutMySql 
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