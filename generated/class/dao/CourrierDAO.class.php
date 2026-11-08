<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface CourrierDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Courrier 
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
 	 * @param courrier primary key
 	 */
	public function delete($ID_COU);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Courrier courrier
 	 */
	public function insert($courrier);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Courrier courrier
 	 */
	public function update($courrier);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByOBJ($value);

	public function queryByDATENR($value);

	public function queryByNBRP($value);

	public function queryByEXP($value);

	public function queryByOBS($value);

	public function queryByChemin($value);


	public function deleteByOBJ($value);

	public function deleteByDATENR($value);

	public function deleteByNBRP($value);

	public function deleteByEXP($value);

	public function deleteByOBS($value);

	public function deleteByChemin($value);


}
?>