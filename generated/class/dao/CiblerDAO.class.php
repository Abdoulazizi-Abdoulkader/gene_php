<?php
/**
 * Intreface DAO
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */
interface CiblerDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Cibler 
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
 	 * @param cibler primary key
 	 */
	public function delete($ID_CIB);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Cibler cibler
 	 */
	public function insert($cibler);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Cibler cibler
 	 */
	public function update($cibler);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIDSER($value);

	public function queryByIDFIC($value);

	public function queryByArchive($value);

	public function queryByVue($value);


	public function deleteByIDSER($value);

	public function deleteByIDFIC($value);

	public function deleteByArchive($value);

	public function deleteByVue($value);


}
?>