<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface EntrantDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Entrant 
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
 	 * @param entrant primary key
 	 */
	public function delete($ID_ENT);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Entrant entrant
 	 */
	public function insert($entrant);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Entrant entrant
 	 */
	public function update($entrant);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIDCOU($value);

	public function queryByDATCOU($value);

	public function queryByNumOrd($value);


	public function deleteByIDCOU($value);

	public function deleteByDATCOU($value);

	public function deleteByNumOrd($value);


}
?>