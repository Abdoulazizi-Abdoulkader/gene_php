<?php
/**
 * Class that operate on table 'courrier'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class CourrierMySqlDAO implements CourrierDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return CourrierMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM courrier WHERE ID_COU = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM courrier';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM courrier ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param courrier primary key
 	 */
	public function delete($ID_COU){
		$sql = 'DELETE FROM courrier WHERE ID_COU = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_COU);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param CourrierMySql courrier
 	 */
	public function insert($courrier){
		$sql = 'INSERT INTO courrier (OBJ, DAT_ENR, NBR_P, EXP, OBS, chemin) VALUES (?, ?, ?, ?, ?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($courrier->oBJ);
		$sqlQuery->set($courrier->dATENR);
		$sqlQuery->setNumber($courrier->nBRP);
		$sqlQuery->set($courrier->eXP);
		$sqlQuery->set($courrier->oBS);
		$sqlQuery->set($courrier->chemin);

		$id = $this->executeInsert($sqlQuery);	
		$courrier->iDCOU = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param CourrierMySql courrier
 	 */
	public function update($courrier){
		$sql = 'UPDATE courrier SET OBJ = ?, DAT_ENR = ?, NBR_P = ?, EXP = ?, OBS = ?, chemin = ? WHERE ID_COU = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($courrier->oBJ);
		$sqlQuery->set($courrier->dATENR);
		$sqlQuery->setNumber($courrier->nBRP);
		$sqlQuery->set($courrier->eXP);
		$sqlQuery->set($courrier->oBS);
		$sqlQuery->set($courrier->chemin);

		$sqlQuery->setNumber($courrier->iDCOU);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM courrier';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByOBJ($value){
		$sql = 'SELECT * FROM courrier WHERE OBJ = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByDATENR($value){
		$sql = 'SELECT * FROM courrier WHERE DAT_ENR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByNBRP($value){
		$sql = 'SELECT * FROM courrier WHERE NBR_P = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByEXP($value){
		$sql = 'SELECT * FROM courrier WHERE EXP = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByOBS($value){
		$sql = 'SELECT * FROM courrier WHERE OBS = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByChemin($value){
		$sql = 'SELECT * FROM courrier WHERE chemin = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByOBJ($value){
		$sql = 'DELETE FROM courrier WHERE OBJ = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByDATENR($value){
		$sql = 'DELETE FROM courrier WHERE DAT_ENR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByNBRP($value){
		$sql = 'DELETE FROM courrier WHERE NBR_P = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByEXP($value){
		$sql = 'DELETE FROM courrier WHERE EXP = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByOBS($value){
		$sql = 'DELETE FROM courrier WHERE OBS = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByChemin($value){
		$sql = 'DELETE FROM courrier WHERE chemin = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return CourrierMySql 
	 */
	protected function readRow($row){
		$courrier = new Courrier();
		
		$courrier->iDCOU = $row['ID_COU'];
		$courrier->oBJ = $row['OBJ'];
		$courrier->dATENR = $row['DAT_ENR'];
		$courrier->nBRP = $row['NBR_P'];
		$courrier->eXP = $row['EXP'];
		$courrier->oBS = $row['OBS'];
		$courrier->chemin = $row['chemin'];

		return $courrier;
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
	 * @return CourrierMySql 
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