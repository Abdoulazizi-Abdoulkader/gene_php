<?php
/**
 * Class that operate on table 'cibler'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class CiblerMySqlDAO implements CiblerDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return CiblerMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM cibler WHERE ID_CIB = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM cibler';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM cibler ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param cibler primary key
 	 */
	public function delete($ID_CIB){
		$sql = 'DELETE FROM cibler WHERE ID_CIB = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_CIB);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param CiblerMySql cibler
 	 */
	public function insert($cibler){
		$sql = 'INSERT INTO cibler (ID_SER, ID_FIC, archive, vue) VALUES (?, ?, ?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($cibler->iDSER);
		$sqlQuery->setNumber($cibler->iDFIC);
		$sqlQuery->setNumber($cibler->archive);
		$sqlQuery->setNumber($cibler->vue);

		$id = $this->executeInsert($sqlQuery);	
		$cibler->iDCIB = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param CiblerMySql cibler
 	 */
	public function update($cibler){
		$sql = 'UPDATE cibler SET ID_SER = ?, ID_FIC = ?, archive = ?, vue = ? WHERE ID_CIB = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($cibler->iDSER);
		$sqlQuery->setNumber($cibler->iDFIC);
		$sqlQuery->setNumber($cibler->archive);
		$sqlQuery->setNumber($cibler->vue);

		$sqlQuery->setNumber($cibler->iDCIB);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM cibler';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByIDSER($value){
		$sql = 'SELECT * FROM cibler WHERE ID_SER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByIDFIC($value){
		$sql = 'SELECT * FROM cibler WHERE ID_FIC = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByArchive($value){
		$sql = 'SELECT * FROM cibler WHERE archive = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByVue($value){
		$sql = 'SELECT * FROM cibler WHERE vue = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByIDSER($value){
		$sql = 'DELETE FROM cibler WHERE ID_SER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByIDFIC($value){
		$sql = 'DELETE FROM cibler WHERE ID_FIC = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByArchive($value){
		$sql = 'DELETE FROM cibler WHERE archive = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByVue($value){
		$sql = 'DELETE FROM cibler WHERE vue = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return CiblerMySql 
	 */
	protected function readRow($row){
		$cibler = new Cibler();
		
		$cibler->iDSER = $row['ID_SER'];
		$cibler->iDFIC = $row['ID_FIC'];
		$cibler->iDCIB = $row['ID_CIB'];
		$cibler->archive = $row['archive'];
		$cibler->vue = $row['vue'];

		return $cibler;
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
	 * @return CiblerMySql 
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