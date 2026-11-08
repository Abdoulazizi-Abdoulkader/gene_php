<?php
/**
 * Class that operate on table 'fiche'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class FicheMySqlDAO implements FicheDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return FicheMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM fiche WHERE ID_FIC = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM fiche';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM fiche ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param fiche primary key
 	 */
	public function delete($ID_FIC){
		$sql = 'DELETE FROM fiche WHERE ID_FIC = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_FIC);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param FicheMySql fiche
 	 */
	public function insert($fiche){
		$sql = 'INSERT INTO fiche (ID_CAR, ID_CON, obser) VALUES (?, ?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($fiche->iDCAR);
		$sqlQuery->setNumber($fiche->iDCON);
		$sqlQuery->set($fiche->obser);

		$id = $this->executeInsert($sqlQuery);	
		$fiche->iDFIC = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param FicheMySql fiche
 	 */
	public function update($fiche){
		$sql = 'UPDATE fiche SET ID_CAR = ?, ID_CON = ?, obser = ? WHERE ID_FIC = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($fiche->iDCAR);
		$sqlQuery->setNumber($fiche->iDCON);
		$sqlQuery->set($fiche->obser);

		$sqlQuery->setNumber($fiche->iDFIC);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM fiche';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByIDCAR($value){
		$sql = 'SELECT * FROM fiche WHERE ID_CAR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByIDCON($value){
		$sql = 'SELECT * FROM fiche WHERE ID_CON = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByObser($value){
		$sql = 'SELECT * FROM fiche WHERE obser = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByIDCAR($value){
		$sql = 'DELETE FROM fiche WHERE ID_CAR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByIDCON($value){
		$sql = 'DELETE FROM fiche WHERE ID_CON = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByObser($value){
		$sql = 'DELETE FROM fiche WHERE obser = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return FicheMySql 
	 */
	protected function readRow($row){
		$fiche = new Fiche();
		
		$fiche->iDFIC = $row['ID_FIC'];
		$fiche->iDCAR = $row['ID_CAR'];
		$fiche->iDCON = $row['ID_CON'];
		$fiche->obser = $row['obser'];

		return $fiche;
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
	 * @return FicheMySql 
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