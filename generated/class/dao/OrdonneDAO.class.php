<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface OrdonneDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Ordonne 
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
 	 * @param ordonne primary key
 	 */
	public function delete($ID_ORD);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Ordonne ordonne
 	 */
	public function insert($ordonne);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Ordonne ordonne
 	 */
	public function update($ordonne);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIDDIR($value);

	public function queryByIDINS($value);


	public function deleteByIDDIR($value);

	public function deleteByIDINS($value);


}
?>