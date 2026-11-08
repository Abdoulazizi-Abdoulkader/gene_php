<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface CompteDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Compte 
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
 	 * @param compte primary key
 	 */
	public function delete($ID_USE);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Compte compte
 	 */
	public function insert($compte);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Compte compte
 	 */
	public function update($compte);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIDPER($value);

	public function queryByLOGIN($value);

	public function queryByPWD($value);

	public function queryByIDPRO($value);


	public function deleteByIDPER($value);

	public function deleteByLOGIN($value);

	public function deleteByPWD($value);

	public function deleteByIDPRO($value);


}
?>