<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface ServiceDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Service 
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
 	 * @param service primary key
 	 */
	public function delete($ID_SER);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Service service
 	 */
	public function insert($service);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Service service
 	 */
	public function update($service);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIDDIR($value);

	public function queryByNOMSER($value);


	public function deleteByIDDIR($value);

	public function deleteByNOMSER($value);


}
?>