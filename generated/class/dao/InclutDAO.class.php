<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface InclutDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Inclut 
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
 	 * @param inclut primary key
 	 */
	public function delete($ID_INC);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Inclut inclut
 	 */
	public function insert($inclut);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Inclut inclut
 	 */
	public function update($inclut);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIDFIC($value);

	public function queryByIDINS($value);


	public function deleteByIDFIC($value);

	public function deleteByIDINS($value);


}
?>