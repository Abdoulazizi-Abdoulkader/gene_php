<?php

/**
 * DAOFactory
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: ${date}
 */
class DAOFactory{
	
	/**
	 * @return CaractereDAO
	 */
	public static function getCaractereDAO(){
		return new CaractereMySqlExtDAO();
	}

	/**
	 * @return CiblerDAO
	 */
	public static function getCiblerDAO(){
		return new CiblerMySqlExtDAO();
	}

	/**
	 * @return CompteDAO
	 */
	public static function getCompteDAO(){
		return new CompteMySqlExtDAO();
	}

	/**
	 * @return ConcerneDAO
	 */
	public static function getConcerneDAO(){
		return new ConcerneMySqlExtDAO();
	}

	/**
	 * @return CourrierDAO
	 */
	public static function getCourrierDAO(){
		return new CourrierMySqlExtDAO();
	}

	/**
	 * @return DirectionDAO
	 */
	public static function getDirectionDAO(){
		return new DirectionMySqlExtDAO();
	}

	/**
	 * @return EntrantDAO
	 */
	public static function getEntrantDAO(){
		return new EntrantMySqlExtDAO();
	}

	/**
	 * @return FicheDAO
	 */
	public static function getFicheDAO(){
		return new FicheMySqlExtDAO();
	}

	/**
	 * @return InclutDAO
	 */
	public static function getInclutDAO(){
		return new InclutMySqlExtDAO();
	}

	/**
	 * @return IncremaDAO
	 */
	public static function getIncremaDAO(){
		return new IncremaMySqlExtDAO();
	}

	/**
	 * @return InstructionDAO
	 */
	public static function getInstructionDAO(){
		return new InstructionMySqlExtDAO();
	}

	/**
	 * @return OrdonneDAO
	 */
	public static function getOrdonneDAO(){
		return new OrdonneMySqlExtDAO();
	}

	/**
	 * @return PersonneDAO
	 */
	public static function getPersonneDAO(){
		return new PersonneMySqlExtDAO();
	}

	/**
	 * @return ProfileDAO
	 */
	public static function getProfileDAO(){
		return new ProfileMySqlExtDAO();
	}

	/**
	 * @return RepondDAO
	 */
	public static function getRepondDAO(){
		return new RepondMySqlExtDAO();
	}

	/**
	 * @return ServiceDAO
	 */
	public static function getServiceDAO(){
		return new ServiceMySqlExtDAO();
	}

	/**
	 * @return SortantDAO
	 */
	public static function getSortantDAO(){
		return new SortantMySqlExtDAO();
	}


}
?>