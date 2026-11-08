<?php
/**
 * Class that operate on table 'instruction'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
class InstructionMySqlDAO implements InstructionDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return InstructionMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM instruction WHERE ID_INS = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM instruction';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM instruction ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param instruction primary key
 	 */
	public function delete($ID_INS){
		$sql = 'DELETE FROM instruction WHERE ID_INS = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($ID_INS);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param InstructionMySql instruction
 	 */
	public function insert($instruction){
		$sql = 'INSERT INTO instruction (LIB_INS) VALUES (?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($instruction->lIBINS);

		$id = $this->executeInsert($sqlQuery);	
		$instruction->iDINS = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param InstructionMySql instruction
 	 */
	public function update($instruction){
		$sql = 'UPDATE instruction SET LIB_INS = ? WHERE ID_INS = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($instruction->lIBINS);

		$sqlQuery->setNumber($instruction->iDINS);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM instruction';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByLIBINS($value){
		$sql = 'SELECT * FROM instruction WHERE LIB_INS = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByLIBINS($value){
		$sql = 'DELETE FROM instruction WHERE LIB_INS = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return InstructionMySql 
	 */
	protected function readRow($row){
		$instruction = new Instruction();
		
		$instruction->iDINS = $row['ID_INS'];
		$instruction->lIBINS = $row['LIB_INS'];

		return $instruction;
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
	 * @return InstructionMySql 
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