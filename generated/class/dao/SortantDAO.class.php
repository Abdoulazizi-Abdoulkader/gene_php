<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface SortantDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Sortant 
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
 	 * @param sortant primary key
 	 */
	public function delete($ID_SOR);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Sortant sortant
 	 */
	public function insert($sortant);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Sortant sortant
 	 */
	public function update($sortant);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIDCOU($value);

	public function queryByDES($value);


	public function deleteByIDCOU($value);

	public function deleteByDES($value);


}
?>