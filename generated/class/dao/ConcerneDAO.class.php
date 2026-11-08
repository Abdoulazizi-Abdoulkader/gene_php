<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface ConcerneDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Concerne 
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
 	 * @param concerne primary key
 	 */
	public function delete($ID_CON);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Concerne concerne
 	 */
	public function insert($concerne);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Concerne concerne
 	 */
	public function update($concerne);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIDDIR($value);

	public function queryByIDCOU($value);

	public function queryByArchive($value);

	public function queryByTraite($value);


	public function deleteByIDDIR($value);

	public function deleteByIDCOU($value);

	public function deleteByArchive($value);

	public function deleteByTraite($value);


}
?>