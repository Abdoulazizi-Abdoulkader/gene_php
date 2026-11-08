<?php
/**
 * Class that operate on table 'service'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class ServiceMySqlDAO implements ServiceDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return ServiceMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM service WHERE ID_SER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM service';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM service ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param service primary key
 	 */
	public function delete($ID_SER){
		$sql = 'DELETE FROM service WHERE ID_SER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_SER);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param ServiceMySql service
 	 */
	public function insert($service){
		$sql = 'INSERT INTO service (ID_DIR, NOM_SER) VALUES (?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($service->iDDIR);
		$sqlQuery->set($service->nOMSER);

		$id = $this->executeInsert($sqlQuery);	
		$service->iDSER = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param ServiceMySql service
 	 */
	public function update($service){
		$sql = 'UPDATE service SET ID_DIR = ?, NOM_SER = ? WHERE ID_SER = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->setNumber($service->iDDIR);
		$sqlQuery->set($service->nOMSER);

		$sqlQuery->setNumber($service->iDSER);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM service';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByIDDIR($value){
		$sql = 'SELECT * FROM service WHERE ID_DIR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByNOMSER($value){
		$sql = 'SELECT * FROM service WHERE NOM_SER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByIDDIR($value){
		$sql = 'DELETE FROM service WHERE ID_DIR = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByNOMSER($value){
		$sql = 'DELETE FROM service WHERE NOM_SER = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return ServiceMySql 
	 */
	protected function readRow($row){
		$service = new Service();
		
		$service->iDSER = $row['ID_SER'];
		$service->iDDIR = $row['ID_DIR'];
		$service->nOMSER = $row['NOM_SER'];

		return $service;
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
	 * @return ServiceMySql 
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