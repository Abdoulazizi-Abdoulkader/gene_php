<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface ProfileDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Profile 
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
 	 * @param profile primary key
 	 */
	public function delete($ID_PRO);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Profile profile
 	 */
	public function insert($profile);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Profile profile
 	 */
	public function update($profile);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByNOMPRO($value);


	public function deleteByNOMPRO($value);


}
?>