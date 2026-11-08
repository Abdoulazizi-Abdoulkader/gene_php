<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface CaractereDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Caractere 
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
 	 * @param caractere primary key
 	 */
	public function delete($ID_CAR);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Caractere caractere
 	 */
	public function insert($caractere);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Caractere caractere
 	 */
	public function update($caractere);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByLIBCAR($value);


	public function deleteByLIBCAR($value);


}
?>