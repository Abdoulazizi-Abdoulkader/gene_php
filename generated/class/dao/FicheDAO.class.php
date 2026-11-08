<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface FicheDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Fiche 
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
 	 * @param fiche primary key
 	 */
	public function delete($ID_FIC);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Fiche fiche
 	 */
	public function insert($fiche);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Fiche fiche
 	 */
	public function update($fiche);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIDCAR($value);

	public function queryByIDCON($value);

	public function queryByObser($value);


	public function deleteByIDCAR($value);

	public function deleteByIDCON($value);

	public function deleteByObser($value);


}
?>