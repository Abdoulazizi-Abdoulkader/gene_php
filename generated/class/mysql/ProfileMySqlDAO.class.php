<?php
/**
 * Class that operate on table 'profile'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class ProfileMySqlDAO implements ProfileDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return ProfileMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM profile WHERE ID_PRO = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM profile';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM profile ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param profile primary key
 	 */
	public function delete($ID_PRO){
		$sql = 'DELETE FROM profile WHERE ID_PRO = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($ID_PRO);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param ProfileMySql profile
 	 */
	public function insert($profile){
		$sql = 'INSERT INTO profile (NOM_PRO) VALUES (?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($profile->nOMPRO);

		$id = $this->executeInsert($sqlQuery);	
		$profile->iDPRO = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param ProfileMySql profile
 	 */
	public function update($profile){
		$sql = 'UPDATE profile SET NOM_PRO = ? WHERE ID_PRO = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($profile->nOMPRO);

		$sqlQuery->set($profile->iDPRO);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM profile';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByNOMPRO($value){
		$sql = 'SELECT * FROM profile WHERE NOM_PRO = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByNOMPRO($value){
		$sql = 'DELETE FROM profile WHERE NOM_PRO = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return ProfileMySql 
	 */
	protected function readRow($row){
		$profile = new Profile();
		
		$profile->iDPRO = $row['ID_PRO'];
		$profile->nOMPRO = $row['NOM_PRO'];

		return $profile;
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
	 * @return ProfileMySql 
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