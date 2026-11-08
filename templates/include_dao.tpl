<?php
	//include all DAO files
	require_once('class/sql/Connection.class.php');
	require_once('class/sql/ConnectionFactory.class.php');
	require_once('class/sql/QueryExecutor.class.php');
	require_once('class/sql/Transaction.class.php');
	require_once('class/sql/SqlQuery.class.php');
	require_once('class/core/ArrayList.class.php');
	require_once('class/dao/DAOFactory.class.php');

	require_once('Framework/Configuration.php');
	require_once('Framework/ControleurSimple.php');
	require_once('Framework/Controleur.php');
	require_once('Framework/Modele.php');
	require_once('Framework/Requete.php');
	require_once('Framework/Routeur.php');
	require_once('Framework/Session.php');
	require_once('Framework/Vue.php');
	
 	${include}
?>