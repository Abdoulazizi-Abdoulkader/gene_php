<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface RepondDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Repond 
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
 	 * @param repond primary key
 	 */
	public function delete($ID_REP);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Repond repond
 	 */
	public function insert($repond);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Repond repond
 	 */
	public function update($repond);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIDSOR($value);

	public function queryByIDENT($value);


	public function deleteByIDSOR($value);

	public function deleteByIDENT($value);


}
?>