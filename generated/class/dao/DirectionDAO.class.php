<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface DirectionDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Direction 
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
 	 * @param direction primary key
 	 */
	public function delete($ID_DIR);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Direction direction
 	 */
	public function insert($direction);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Direction direction
 	 */
	public function update($direction);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByNOMDIR($value);


	public function deleteByNOMDIR($value);


}
?>