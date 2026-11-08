<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface PersonneDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Personne 
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
 	 * @param personne primary key
 	 */
	public function delete($ID_PER);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Personne personne
 	 */
	public function insert($personne);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Personne personne
 	 */
	public function update($personne);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIDSER($value);

	public function queryByNOMPER($value);

	public function queryByPRENOM($value);

	public function queryByAGE($value);

	public function queryByTEL($value);

	public function queryByMAIL($value);


	public function deleteByIDSER($value);

	public function deleteByNOMPER($value);

	public function deleteByPRENOM($value);

	public function deleteByAGE($value);

	public function deleteByTEL($value);

	public function deleteByMAIL($value);


}
?>