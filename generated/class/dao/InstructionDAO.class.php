<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface InstructionDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Instruction 
	 */
	public function load($id);

	/**
	 * Get all records from table
	 */
	public function queryAll();
	
	/**
	 * Get all records from table ordered by field
	 * @Param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn);
	
	/**
 	 * Delete record from table
 	 * @param instruction primary key
 	 */
	public function delete($ID_INS);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Instruction instruction
 	 */
	public function insert($instruction);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Instruction instruction
 	 */
	public function update($instruction);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByLIBINS($value);


	public function deleteByLIBINS($value);


}
?>