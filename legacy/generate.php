<?php

require_once('templates/class/dao/sql/Connection.class.php');
require_once('templates/class/dao/sql/ConnectionFactory.class.php');
require_once('templates/class/dao/sql/QueryExecutor.class.php');
require_once('templates/class/dao/sql/Transaction.class.php');
require_once('templates/class/dao/sql/SqlQuery.class.php');
require_once('templates/class/Template.php');
require_once('templates/Framework/Configuration.php');



function generate(){
	init();
	createConfigFile();
	$sql = 'SHOW TABLES';
	$ret = QueryExecutor::execute(new SqlQuery($sql));
	// getnerateDomainObjects($ret);
	// getnerateDAOObjects($ret);
	// getnerateDAOExtObjects($ret);
	// getnerateIDAOObjects($ret);
	createIncludeFile($ret);
	// createManagerFactory($ret);
	getnerateClassObjects($ret);
	getnerateManagers($ret);
	createManagerFactory($ret);
	createVueDir($ret);
	getnerateControleur($ret);
	getnerateVueAjout($ret);
	getnerateVueIndex($ret);
	getnerateVueModification($ret);
	getnerateVueDetails($ret);
	createMenu($ret);
	xcopy("templates/Contenu","".$_POST['nomapp']."/Contenu");
	xcopy($_POST['nomapp'],"../".$_POST['nomapp']);
	
	header("Location: /".$_POST['nomapp']."/");
}

function createVueDir($ret){
	for($i=0;$i<count($ret);$i++){
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName);
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		@mkdir("".$_POST['nomapp']."/Vue/".$clazzName);
		}
	copy('templates/others/Accueil/index.php', ''.$_POST['nomapp'].'/Vue/Accueil/index.php');
}

function init(){
	@mkdir("".$_POST['nomapp']."");
	@mkdir("".$_POST['nomapp']."/class");
	@mkdir("".$_POST['nomapp']."/class/classObjects");
	@mkdir("".$_POST['nomapp']."/class/managerObjects");
	@mkdir("".$_POST['nomapp']."/Vue");
	@mkdir("".$_POST['nomapp']."/Vue/Menu");
	@mkdir("".$_POST['nomapp']."/Vue/Copyright");
	@mkdir("".$_POST['nomapp']."/Framework");
	@mkdir("".$_POST['nomapp']."/Contenu");
	@mkdir("".$_POST['nomapp']."/Config");
	@mkdir("".$_POST['nomapp']."/Controleur");
	@mkdir("".$_POST['nomapp']."/Vue/Accueil");
	// @mkdir("".$_POST['nomapp']."/class/dto");
	// @mkdir("".$_POST['nomapp']."/class/mysql");
	// @mkdir("".$_POST['nomapp']."/class/mysql/ext");
	// @mkdir("".$_POST['nomapp']."/class/sql");
	// @mkdir("".$_POST['nomapp']."/class/dao");
	// @mkdir("".$_POST['nomapp']."/class/core");
	
	// copy('templates/class/dao/sql/Connection.class.php', ''.$_POST['nomapp'].'/class/sql/Connection.class.php');
	// copy('templates/class/dao/sql/ConnectionFactory.class.php', ''.$_POST['nomapp'].'/class/sql/ConnectionFactory.class.php');
	// copy('templates/class/dao/sql/QueryExecutor.class.php', ''.$_POST['nomapp'].'/class/sql/QueryExecutor.class.php');
	// copy('templates/class/dao/sql/Transaction.class.php', ''.$_POST['nomapp'].'/class/sql/Transaction.class.php');
	// copy('templates/class/dao/sql/SqlQuery.class.php', ''.$_POST['nomapp'].'/class/sql/SqlQuery.class.php');
	// copy('templates/class/dao/core/ArrayList.class.php', ''.$_POST['nomapp'].'/class/core/ArrayList.class.php');

	copy('templates/Vue/erreur.php', ''.$_POST['nomapp'].'/Vue/erreur.php');
	copy('templates/Vue/gabarit.php', ''.$_POST['nomapp'].'/Vue/gabarit.php');
	copy('templates/Vue/Copyright/copyright.php', ''.$_POST['nomapp'].'/Vue/Copyright/copyright.php');
	copy('templates/others/Configuration.php', ''.$_POST['nomapp'].'/Framework/Configuration.php');
	copy('templates/others/Accueil/ControleurAccueil.php', ''.$_POST['nomapp'].'/Controleur/ControleurAccueil.php');
	copy('templates/others/index.php', ''.$_POST['nomapp'].'/index.php');
	copy('templates/Framework/Controleur.php', ''.$_POST['nomapp'].'/Framework/Controleur.php');
	copy('templates/Framework/ControleurSimple.php', ''.$_POST['nomapp'].'/Framework/ControleurSimple.php');
	copy('templates/Framework/Modele.php', ''.$_POST['nomapp'].'/Framework/Modele.php');
	copy('templates/Framework/Requete.php', ''.$_POST['nomapp'].'/Framework/Requete.php');
	copy('templates/Framework/Routeur.php', ''.$_POST['nomapp'].'/Framework/Routeur.php');
	copy('templates/Framework/Session.php', ''.$_POST['nomapp'].'/Framework/Session.php');
	copy('templates/Framework/Vue.php', ''.$_POST['nomapp'].'/Framework/Vue.php');
	copy('templates/.htaccess', ''.$_POST['nomapp'].'/.htaccess');

	
	}

function createIncludeFile($ret){
	$str ="\n\trequire_once('class/managerObjects/ManagersFactory.class.php');\n";
	for($i=0;$i<count($ret);$i++){
		$tableName = $ret[$i][0];
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$clazzName = getClazzName($tableName);
		$str .= "\trequire_once('class/managerObjects/".$clazzName."Manager.php');\n";
		$str .= "\trequire_once('class/classObjects/".$clazzName.".php');\n";
	}
	$template = new Template('templates/include_files.tpl');
	$template->set('include', $str);
	$template->write(''.$_POST['nomapp'].'/include_files.php');
}

function createConfigFile(){
	$template = new Template('templates/config.tpl');
	$template->set('nomapp', $_POST['nomapp']);
	$template->set('bd', $_POST['bd']);
	$template->set('host', $_POST['host']);
	$template->set('mdp', $_POST['mdp']);
	$template->set('login', $_POST['login']);
	$template->write(''.$_POST['nomapp'].'/Config/dev.ini');
	
	copy($_POST['nomapp'].'/Config/dev.ini','templates/Config/dev.ini');

}

function createMenu($ret){
	$str ="\n";
	$drapo=0;
	for($i=0;$i<count($ret);$i++){
		$tableName = $ret[$i][0];
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$clazzName = getClazzName($tableName);
		
		if($i<=3 || count($ret)<=4)
		$str .= "\t<li id=\"".$tableName."\" class=\"menu <?php if (isset(\$".$tableName.")) echo 'lien-courant';?>\"><a href=\"".$tableName."/\" ><i class=\"icon-list\" ></i> ".$tableName."</a></li>\n";
		
		if($i>3 && count($ret)>4){
		if($drapo==0){
		$str .="<li class=\"dropdown menu\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\"><i class=\"icon-cogs\"></i> Paramètres <b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">";
		$drapo=1;
		}
        $str .="<li><a href=\"".$tableName."/\" ><i class=\"icon-list\"></i> ".$tableName."</a></li>";
		}
		
	}
	if($drapo==1){
		$str.="</ul>
            </li>";
		}
		
	$template = new Template('templates/menu.tpl');
	$template->set('content', $str);
	$template->write(''.$_POST['nomapp'].'/Vue/Menu/menu.php');
}

function doesTableContainPK($row){
	$row = getFields($row[0]);
	for($j=0;$j< count($row);$j++){
		if($row[$j][3]=='PRI'){
			return true;
		}
	}
	return false;
}

function createManagerFactory($ret){
	$str ="\n";
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName);
		$str .= "\t/**\n";
		$str .= "\t * @return ".$clazzName."Manager\n";
		$str .= "\t */\n";
		$str .= "\tpublic static function get".$clazzName."Manager(){\n";
		$str .= "\t\treturn new ".$clazzName."Manager();\n";
		$str .= "\t}\n\n";
	}
	$template = new Template('templates/ManagerFactory.tpl');
	$template->set('content', $str);
	$template->set('date', date("Y-m-d H:i"));
	$template->write(''.$_POST['nomapp'].'/class/managerObjects/ManagersFactory.class.php');
}

/**
 * Enter description here...
 *
 * @param unknown_type $ret
 * @return
 */
function getnerateDomainObjects($ret){
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName);
		if($clazzName[strlen($clazzName)-1]=='s'){
			$clazzName = substr($clazzName, 0, strlen($clazzName)-1);
		}
		$template = new Template('templates/Domain.tpl');
		$template->set('domain_class_name', $clazzName);
		$template->set('table_name', $tableName);
		$tab = getFields($tableName);
		$fields = "\r\n";
		for($j=0;$j<count($tab);$j++){
			$fields .= "\t\tvar $".getVarNameWithS($tab[$j][0]).";\n\r";
		}
		$template->set('variables', $fields);
		$template->set('date', date("Y-m-d H:i"));
		$template->write(''.$_POST['nomapp'].'/class/dto/'.$clazzName.'.class.php');
	}
}

function getnerateClassObjects($ret){
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName);
		if($clazzName[strlen($clazzName)-1]=='s'){
			$clazzName = substr($clazzName, 0, strlen($clazzName)-1);
		}
		$template = new Template('templates/Class_template.tpl');
		$template->set('domain_class_name', ucfirst($tableName));
		$template->set('table_name', $tableName);
		$tab = getFields($tableName);
		$fields = "\r\n";
		for($j=0;$j<count($tab);$j++){
			$fields .= "\t\tprotected $".strtolower($tab[$j][0]).";\r";
		}
		$getter_setter = "\r\n";
		for($j=0;$j<count($tab);$j++){
			$getter_setter .= "\t\tpublic function get".getClazzName(strtolower($tab[$j][0]))."(){\r\t\t\treturn \$this->".strtolower($tab[$j][0]).";\r\t\t}\n\n";
			$getter_setter .= "\t\tpublic function set".getClazzName(strtolower($tab[$j][0]))."($".strtolower($tab[$j][0])."){\r\t\t\t\$this->".strtolower($tab[$j][0])." = $".strtolower($tab[$j][0]).";\r\t\t}\n\n";
		}
		$template->set('variables', $fields);
		$template->set('getter_setter', $getter_setter);
		$template->set('date', date("Y-m-d H:i"));
		$template->write(''.$_POST['nomapp'].'/class/classObjects/'.ucfirst($tableName).'.php');
	}
}

function getnerateVueIndex($ret){
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName);
		$template = new Template('templates/index.tpl');
		$template->set('domain_class_name', $clazzName);
		$template->set('table_name', $tableName);
		$tab = getFields($tableName);
		$table_head = "<tr>\n";
		$table_content = "<tr>";
		for($j=0;$j<count($tab);$j++){
		$ref=getRefTableName($tableName,$tab[$j][0]);
		$col=getRefColumnName($tableName,$tab[$j][0]);
		
		/*if(count($ref)!=0){
			$table_head .= '<th class="th">'.($tab[$j][0]).'\';
		$donneeSelect = ManagersFactory::get'.getClazzName(strtolower($ref)).'Manager()->queryAllByQuery("
		SELECT *
		FROM '.strtolower($ref).'
		WHERE '.$col.'
		 IN (SELECT DISTINCT '.$tab[$j][0].' FROM '.$clazzName.') ORDER BY '.$col.'");
		
		echo  \'<select class="form-control rechFiltre" name="filtreLoc" id="filtreLoc" onchange="go(\\\'rechercheBagri\\\',this.value);">
		<option value="">Choisissez un(e) '.getClazzName(strtolower($ref)).'</option>\';
		for($i=0;$i<count($donneeSelect);$i++){
			$a=$donneeSelect[$i];
			echo \'<option value="\'.$a->get'.getClazzName(strtolower($col)).'().\'">\'.htmlentities($a->get'.getClazzName(strtolower($col)).'()).\'</option>\';
			}
echo \'</select>
			</th>';
		}
		else*/
			$table_head .= "\t<th class=\"th\">".getClazzName(strtolower($tab[$j][0]))."</th>";
			
			if($j == 9) $j = count($tab);
		}
		$table_head.="\t<th class=\"th last\">Actions</th></tr></thead>\n<tbody id=\"corpsTab\">";
		$pk="";
		for($j=0;$j<count($tab);$j++){
		if($tab[$j][3]=='PRI'){
				$pk = strtolower($tab[$j][0]);
			}		
		if($j==0)
		$table_content .= "
<td style=\"font-weight:bold\">'.htmlentities(\$d->get".getClazzName(strtolower($tab[$j][0]))."()).'</td>";
		else
			$table_content .= "<td>'.htmlentities(\$d->get".getClazzName(strtolower($tab[$j][0]))."()).'</td>";
			
			if($j == 9) $j = count($tab);
		}
		$table_content .= "
		<td><form name=\"form\" action=\"".$tableName."\\modification\" method=\"POST\">
<input type=\"hidden\" class=\"hidden\" id=\"id\" name=\"id\" value=\"'.\$d->get";

if($pk!="") 
echo $table_content .= ucfirst($pk)."().'\">";
else
echo $table_content .= getClazzName(strtolower($tab[$j][0]))."().'\">";
$table_content .= "
<div class=\"btn-group\">
<button type=\"submit\" value=\"btn_view\" id=\"btn\" name=\"btn\" class=\"btn btn-default btn-sm btn-primary\"><i class=\"icon-eye-open\"></i></button>
<button type=\"submit\" value=\"btn_edit\" id=\"btn\" name=\"btn\" class=\"btn btn-default btn-sm btn-warning\"><i class=\"icon-pencil\"></i></button>
</div>
</form></td>";

				$table_content .= "</tr>";
		$template->set('table_head', $table_head);
		$template->set('table_content', $table_content);
		$template->set('date', date("Y-m-d H:i"));
		$template->write(''.$_POST['nomapp'].'/Vue/'.$clazzName.'/index.php');
	}
}

function getnerateVueAjout($ret){
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$flagDate=0;
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName);
		$template = new Template('templates/ajout.tpl');
		$template->set('table_name', $tableName);
		$tab = getFields($tableName);
		$content = "\n";
		for($j=0;$j<count($tab);$j++){
		$ref=getRefTableName($tableName,$tab[$j][0]);
		$col=getRefColumnName($tableName,$tab[$j][0]);

		if($ref!= null && count($ref)!=0){
			$content.= "<div class=\"form-group\"> 
\t<label for=\"".($tab[$j][0])."\" class=\"col-sm-4 control-label\">".getClazzName(strtolower($tab[$j][0]))." :</label>
\t<div class=\"col-sm-6\">
\t\t<select class=\"form-control\" name=\"".($tab[$j][0])."\" id=\"".($tab[$j][0])."\">
\t\t\t<option value=\"\">Choisissez un(e) ".getClazzName(strtolower($ref))."</option>
\t\t\t<?php
\t\t\t\t\$v=ManagersFactory::get".getClazzName(strtolower($ref))."Manager()->queryAll();
\t\t\t\tfor(\$i=0;\$i<count(\$v);\$i++){
\t\t\t\t\techo '<option value=\"'.\$v[\$i]->get".getClazzName(strtolower($col))."().'\">'.\$v[\$i]->get".getClazzName(strtolower($col))."().'</option>';
\t\t\t\t}
\t\t\t?>	
\t\t</select> 
\t</div> 
</div>\n\n"; 
}
else 
if($tab[$j][1]=='date'){
 $content.='<div class="form-group"> <label for="date_'.($tab[$j][0]).'" class="col-sm-4 control-label">'.getClazzName(strtolower($tab[$j][0])).' : </label> 
 <div class="col-sm-6">
<div class="input-group date form_date" data-date="" data-date-format="dd MM yyyy" data-link-field="dATEDD" data-link-format="yyyy-mm-dd">
<input class="form-control" type="text" name="date_'.($tab[$j][0]).'" id="date_'.($tab[$j][0]).'" placeholder="'.($tab[$j][0]).'" readonly>
   <span class="input-group-addon"><span class="glyphicon glyphicon-calendar icon-calendar"></span></span>
   </div>
	<input type="hidden"  id="'.($tab[$j][0]).'" name="'.($tab[$j][0]).'" value="" />
 </div> 
 </div>';
 $flagDate=1;
}
		else{
			$content.= "<div class=\"form-group\"> 
\t<label for=\"".($tab[$j][0])."\" class=\"col-sm-4 control-label\">".getClazzName(strtolower($tab[$j][0]))." :</label>
\t<div class=\"col-sm-6\">
\t\t<input type=\"text\" class=\"form-control\" id=\"".($tab[$j][0])."\" name=\"".($tab[$j][0])."\" required placeholder=\"".($tab[$j][0])."\"> 
\t</div> 
</div>\n\n"; 
		}
		}
		if($flagDate == 1){
		$content.= '<?php $this->scriptJS = \'<script src="Contenu/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
					<script src="Contenu/js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script><script type="text/javascript">$(document).ready(function() {$(".form_date").datetimepicker({
        language:  "fr",
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
});
</script>

\';
$this->feuilleDeStyle = \'<link href="Contenu/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">\';
?>';}
		$template->set('content', $content);
		$template->set('date', date("Y-m-d H:i"));
		$template->write(''.$_POST['nomapp'].'/Vue/'.$clazzName.'/ajout.php');
	}
}

function getnerateVueModification($ret){
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$flagDate=0;
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName);
		$template = new Template('templates/modif.tpl');
		$template->set('table_name', $tableName);
		$tab = getFields($tableName);
		$content = "\n";
		for($j=0;$j<count($tab);$j++){
		$ref=getRefTableName($tableName,$tab[$j][0]);
		$col=getRefColumnName($tableName,$tab[$j][0]);
		if($ref!=null && count($ref)!=0){
			$content.= "<div class=\"form-group\"> 
\t<label for=\"".($tab[$j][0])."\" class=\"col-sm-4 control-label\">".getClazzName(strtolower($tab[$j][0]))." :</label>
\t<div class=\"col-sm-6\">
\t\t<select class=\"form-control\" name=\"".($tab[$j][0])."\" id=\"".($tab[$j][0])."\">
\t\t\t<option value=\"\">Choisissez un(e) ".getClazzName(strtolower($ref))."</option>
\t\t\t<?php
\t\t\t\t\$v=ManagersFactory::get".getClazzName(strtolower($ref))."Manager()->queryAll();
\t\t\t\tfor(\$i=0;\$i<count(\$v);\$i++){
\t\t\t\t\techo '<option value=\"'.\$v[\$i]->get".getClazzName(strtolower($col))."().'\"';
\t\t\t\t\tif(\$".$tableName."->get".getClazzName(strtolower($tab[$j][0]))."()==\$v[\$i]->get".getClazzName(strtolower($col))."())
\t\t\t\t\t\techo 'selected=\"true\"';
\t\t\t\t\t\techo '>'.\$v[\$i]->get".getClazzName(strtolower($tab[$j][0]))."().'</option>';
\t\t\t\t}
\t\t\t?>	
\t\t</select> 
\t</div> 
</div>\n\n"; 
}
else 
if($tab[$j][1]=='date'){
 $content.='<div class="form-group"> <label for="date_'.($tab[$j][0]).'" class="col-sm-4 control-label">'.getClazzName(strtolower($tab[$j][0])).' : </label> 
 <div class="col-sm-6">
<div class="input-group date form_date" data-date="" data-date-format="dd MM yyyy" data-link-field="dATEDD" data-link-format="yyyy-mm-dd">
<input class="form-control" type="text" name="date_'.($tab[$j][0]).'" id="date_'.($tab[$j][0]).'" placeholder="'.($tab[$j][0]).'" readonly value="<?php if(date(\'d M Y\',strtotime($'.$tableName.'->get'.getClazzName(strtolower($tab[$j][0])).'()))!=date(\'d M Y\',strtotime(\'0000-00-00 00:00:00\'))) echo (date(\'d M Y\',strtotime($'.$tableName.'->get'.getClazzName(strtolower($tab[$j][0])).'()))); ?>">
   <span class="input-group-addon"><span class="glyphicon glyphicon-calendar icon-calendar"></span></span>
   </div>
	<input type="hidden"  id="'.($tab[$j][0]).'" name="'.($tab[$j][0]).'" value="<?php echo $'.$tableName.'->get'.getClazzName(strtolower($tab[$j][0])).'(); ?>" />
 </div> 
 </div>';
$flagDate=1;
}

		else{
			$content.= "<div class=\"form-group\"> 
\t<label for=\"".($tab[$j][0])."\" class=\"col-sm-4 control-label\">".getClazzName(strtolower($tab[$j][0]))." :</label>
\t<div class=\"col-sm-6\">
\t\t<input type=\"text\" class=\"form-control\" value=\"<?php echo \$".$tableName."->get".getClazzName(strtolower($tab[$j][0]))."(); ?>\" id=\"".($tab[$j][0])."\" name=\"".($tab[$j][0])."\" required placeholder=\"".($tab[$j][0])."\"> 
\t</div> 
</div>\n\n"; 
		}
		}
		if($flagDate == 1){
		$content.= '<?php $this->scriptJS = \'<script src="Contenu/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
					<script src="Contenu/js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script><script type="text/javascript">$(document).ready(function() {$(".form_date").datetimepicker({
        language:  "fr",
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
});
</script>

\';
$this->feuilleDeStyle = \'<link href="Contenu/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">\';
?>';}
		$template->set('content', $content);
		$template->set('date', date("Y-m-d H:i"));
		$template->write(''.$_POST['nomapp'].'/Vue/'.$clazzName.'/modification.php');
	}
}

function getnerateVueDetails($ret){
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName);
		$template = new Template('templates/details.tpl');
		$template->set('table_name', $tableName);
		$tab = getFields($tableName);
		$content = "\n";
		for($j=0;$j<count($tab);$j++){
		$ref=getRefTableName($tableName,$tab[$j][0]);
		$col=getRefColumnName($tableName,$tab[$j][0]);
		if($ref!=null && count($ref)!=0){
			$content.= "<div class=\"form-group form-group-details\"> 
\t<label for=\"".($tab[$j][0])."\" class=\"col-md-5 control-label\">".getClazzName(strtolower($tab[$j][0]))." :</label>
\t<div class=\"col-md-7\">
\t\t\t<?php
\t\t\t\t\$v=ManagersFactory::get".getClazzName(strtolower($ref))."Manager()->queryAll();
\t\t\t\tfor(\$i=0;\$i<count(\$v);\$i++){
\t\t\t\t\tif(\$".$tableName."->get".getClazzName(strtolower($tab[$j][0]))."()==\$v[\$i]->get".getClazzName(strtolower($col))."())
\t\t\t\t\t\techo '<label class=\"control-label lab-detail\">'.\$v[\$i]->get".getClazzName(strtolower($tab[$j][0]))."().'</label>';
\t\t\t\t}
\t\t\t?>	
\t</div> 
</div>\n\n"; 
}
		else{
			$content.= "<div class=\"form-group form-group-details\"> 
\t<label for=\"".($tab[$j][0])."\" class=\"col-md-5 control-label\">".getClazzName(strtolower($tab[$j][0]))." :</label>
\t<div class=\"col-md-7\">
\t\t<label class=\"control-label lab-detail\"><?php echo \$".$tableName."->get".getClazzName(strtolower($tab[$j][0]))."(); ?></label>
\t</div> 
</div>\n\n"; 
		}
		}
		$template->set('content', $content);
		$template->set('date', date("Y-m-d H:i"));
		$template->write(''.$_POST['nomapp'].'/Vue/'.$clazzName.'/details.php');
	}
}

function getnerateControleur($ret){
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName);
		$tab = getFields($tableName);
		$content = "\n";
		$cont = "\$data = array(\n";
		$pk;
		for($j=0;$j<count($tab);$j++){
		if($tab[$j][3]=='PRI'){
				$pk = strtolower($tab[$j][0]);
			}
			$content.= "\t\t\$".strtolower($tab[$j][0]). "= \$this->requete->getParametre(\"".($tab[$j][0])."\");\n";
			$cont.="\t\t\t\t\t\t'".strtolower($tab[$j][0])."' => \$this->requete->getParametre(\"".($tab[$j][0])."\"),\n";
		}
		$content.="\n";
		$cont.= "\t\t\t\t\t);";
		$template = new Template('templates/controleur.tpl');
		$template->set('content', $cont);
		$template->set('domain_class_name', $clazzName);
		$template->set('table_name', $tableName);
		$template->set('pk', ucfirst($pk));
		$template->set('date', date("Y-m-d H:i"));
		$template->write(''.$_POST['nomapp'].'/Controleur/Controleur'.$clazzName.'.php');
	}
}

function getnerateDAOExtObjects($ret){
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName).'MySqlExt';
		$clazzNameSup = getClazzName($tableName).'MySql';
		$template = new Template('templates/DAOExt.tpl');
		$template->set('dao_clazz_sup_name', $clazzNameSup );
		$template->set('dao_clazz_name', $clazzName );
		$template->set('domain_clazz_name', getDTOName($tableName) );
		$template->set('idao_clazz_name', getClazzName($tableName));
		$template->set('table_name', $tableName);
		$template->set('var_name', getVarName($tableName));
		$tab = getFields($tableName);
		$parameterSetter = "\n";
		$insertFields = "";
		$updateFields = "";
		$bindValue = "";
		$readRow = "\n";
		$pk = '';
		$queryByField = '';
		$deleteByField = '';
		for($j=0;$j<count($tab);$j++){
			if($tab[$j][3]=='PRI'){
				$pk = $tab[$j][0];
			}else{
				$insertFields .= $tab[$j][0].", ";
				$updateFields .= $tab[$j][0]." = ?, ";
				$bindValue .= "?, ";
				if(isColumnTypeNumber($tab[$j][1])){
					$parameterSetter .= "\t\t\$sqlQuery->setNumber($".getVarName($tableName)."->".getVarNameWithS($tab[$j][0]).");\n";
				}else{
					$parameterSetter .= "\t\t\$sqlQuery->set($".getVarName($tableName)."->".getVarNameWithS($tab[$j][0]).");\n";
				}
				$parameterSetter2 = '';
				if(isColumnTypeNumber($tab[$j][1])){
					$parameterSetter2 .= "Number";
				}
				$queryByField .= "	public function queryBy".getClazzName($tab[$j][0])."(\$value){
		\$sql = 'SELECT * FROM ".$tableName." WHERE ".$tab[$j][0]." = ?';
		\$sqlQuery = new SqlQuery(\$sql);
		\$sqlQuery->set".$parameterSetter2."(\$value);
		return \$this->getList(\$sqlQuery);
	}\n\n";
				$deleteByField .= "	public function deleteBy".getClazzName($tab[$j][0])."(\$value){
		\$sql = 'DELETE FROM ".$tableName." WHERE ".$tab[$j][0]." = ?';
		\$sqlQuery = new SqlQuery(\$sql);
		\$sqlQuery->set".$parameterSetter2."(\$value);
		return \$this->executeUpdate(\$sqlQuery);
	}\n\n";
			}
			$readRow .= "\t\t\$".getVarName($tableName)."->".getVarNameWithS($tab[$j][0])." = \$row['".$tab[$j][0]."'];\n";
		}
		if($pk==''){
			continue;
		}
		$insertFields = substr($insertFields,0, strlen($insertFields)-2);
		$updateFields = substr($updateFields,0, strlen($updateFields)-2);
		$bindValue = substr($bindValue,0, strlen($bindValue)-2);
		$template->set('pk', $pk);
		$template->set('pk_php', getVarNameWithS($pk));		
		$template->set('insert_fields', $insertFields);
		$template->set('read_row', $readRow);
		$template->set('update_fields', $updateFields);
		$template->set('question_marks', $bindValue);
		$template->set('parameter_setter',$parameterSetter);
		$template->set('read_row',$readRow);
		$template->set('date', date("Y-m-d H:i"));
		$template->set('queryByFieldFunctions',$queryByField);		
		$template->set('deleteByFieldFunctions',$deleteByField);	
		$file = ''.$_POST['nomapp'].'/class/mysql/ext/'.$clazzName.'DAO.class.php';
		if(!file_exists($file)){
			$template->write(''.$_POST['nomapp'].'/class/mysql/ext/'.$clazzName.'DAO.class.php');
		}
	}
}


function getnerateManagers($ret){
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName);

		$tab = getFields($tableName);
		$parameterSetter = "\n";
		$insertFields = "";
		$updateFields = "";
		$insertFieldsBis = "";
		$bindValue = "";
		$readRow = "\$data = array(\n";
		$pk = '';
		$pks = array();
		$queryByField = '';
		$deleteByField = '';
		$pk_type='';
		for($j=0;$j<count($tab);$j++){
			if($tab[$j][3]=='PRI'){
				$pk = $tab[$j][0];
				$c = count($pks);
				$pks[$c] = $tab[$j][0];
				$pk_type = $tab[$j][1];
			}else{
				$insertFields .= $tab[$j][0].", ";
				$updateFields .= $tab[$j][0]." = :".$tab[$j][0].", ";
				$insertFieldsBis .= ":".$tab[$j][0].", ";
				$bindValue .= "\t\t\$q->bindValue(':".$tab[$j][0]."',\$".$tableName."->get".getClazzName(strtolower($tab[$j][0]))."());\n";
				
				$parameterSetter2 = '';
				if(isColumnTypeNumber($tab[$j][1])){
					$parameterSetter2 .= "Number";
				}
				$queryByField .= "	public function queryBy".getClazzName($tab[$j][0])."(\$value){
		\$sql = 'SELECT * FROM ".$tableName." WHERE ".$tab[$j][0]." = ?';
		\$sqlQuery = new SqlQuery(\$sql);
		\$sqlQuery->set".$parameterSetter2."(\$value);
		return \$this->getList(\$sqlQuery);
	}\n\n";
				$deleteByField .= "	public function deleteBy".getClazzName($tab[$j][0])."(\$value){
		\$sql = 'DELETE FROM ".$tableName." WHERE ".$tab[$j][0]." = ?';
		\$sqlQuery = new SqlQuery(\$sql);
		\$sqlQuery->set".$parameterSetter2."(\$value);
		return \$this->executeUpdate(\$sqlQuery);
	}\n\n";
			}
			// $readRow .= "\t\t\$".getVarName($tableName)."->".getVarNameWithS($tab[$j][0])." = \$row['".$tab[$j][0]."'];\n";
			$readRow.="\t\t\t\t\t\t'".strtolower($tab[$j][0])."' =>  \$row['".$tab[$j][0]."'],\n";
		}
		if($pk==''){
			continue;
		}
		if(count($pks)>=1){
			$template = new Template('templates/Manager.tpl');
			$template->set('bindPk', ucfirst(strtolower($pk)));
			echo '$pk_type '.$pk_type.'<br/>';
			if(isColumnTypeNumber($pk_type)){
				$template->set('pk_number', 'Number');
			}else{
				$template->set('pk_number', '');
			}
		}else{			
			$template = new Template('templates/Manager.tpl');
		}
		$readRow.= "\t\t\t\t\t);";
		$template->set('dao_clazz_name', $clazzName );
		$template->set('domain_clazz_name', getDTOName($tableName) );
		$template->set('idao_clazz_name', getClazzName($tableName));
		$template->set('table_name', $tableName);
		$template->set('read_row',$readRow);
		$template->set('var_name', getVarName($tableName));
		
		$insertFields = substr($insertFields,0, strlen($insertFields)-2);
		$insertFieldsBis = substr($insertFieldsBis,0, strlen($insertFieldsBis)-2);
		$updateFields = substr($updateFields,0, strlen($updateFields)-2);
		$template->set('pk', $pk);
		$s = '';
		$s2 = '';
		$s3 = '';
		$s4 = '';
		$insertFields2 = $insertFields;
		$bindValue2 = $bindValue;
		for($z=0;$z<count($pks);$z++){
			$bindValue2.=', ?';			
			if($z>0){
				$s.=', ';								
				$s2.=' AND ';
				$s3.= "\t\t";
			}			
			$insertFields2.=', '.$pks[$z];
			$s .= '$'.getVarNameWithS($pks[$z]);
			$s2 .= $pks[$z].' = ? ';
			$s3 .= '$sqlQuery->setNumber($'.getVarNameWithS($pks[$z]).');';			
			$s3 .= "\n";
			$s4 .= "\n\t\t";
			$s4 .= '$sqlQuery->setNumber($'.getVarName($tableName).'->'.getVarNameWithS($pks[$z]).');';
			$s4 .= "\n";
		}
		if($s[0]==',')$s = substr($s,1);
		if($bindValue2[0]==',')$bindValue2= substr($bindValue2,1);
		if($insertFields2[0]==',')$insertFields2= substr($insertFields2,1);
		$template->set('question_marks2', $bindValue2);
		$template->set('insert_fields2', $insertFields2);
		$template->set('pk_set_update', $s4);
		$template->set('pk_set', $s3);		
		$template->set('pk_where', $s2);
		$template->set('pks', $s);
		$template->set('pk_php', getVarNameWithS($pk));		
		$template->set('insert_fields', $insertFields);
		$template->set('insertFieldsBis', $insertFieldsBis);
		$template->set('read_row', $readRow);
		$template->set('update_fields', $updateFields);
		$template->set('bindValue', $bindValue);
		$template->set('parameter_setter',$parameterSetter);
		$template->set('read_row',$readRow);
		$template->set('date', date("Y-m-d H:i"));
		$template->set('queryByFieldFunctions',$queryByField);		
		$template->set('deleteByFieldFunctions',$deleteByField);	
		$template->write(''.$_POST['nomapp'].'/class/managerObjects/'.$clazzName.'Manager.php');
	}
}


function getnerateDAOObjects($ret){
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName).'MySql';

		$tab = getFields($tableName);
		$parameterSetter = "\n";
		$insertFields = "";
		$updateFields = "";
		$bindValue = "";
		$readRow = "\n";
		$pk = '';
		$pks = array();
		$queryByField = '';
		$deleteByField = '';
		$pk_type='';
		for($j=0;$j<count($tab);$j++){
			if($tab[$j][3]=='PRI'){
				$pk = $tab[$j][0];
				$c = count($pks);
				$pks[$c] = $tab[$j][0];
				$pk_type = $tab[$j][1];
			}else{
				$insertFields .= $tab[$j][0].", ";
				$updateFields .= $tab[$j][0]." = ?, ";
				$bindValue .= "?, ";
				if(isColumnTypeNumber($tab[$j][1])){
					$parameterSetter .= "\t\t\$sqlQuery->setNumber($".getVarName($tableName)."->".getVarNameWithS($tab[$j][0]).");\n";
				}else{
					$parameterSetter .= "\t\t\$sqlQuery->set($".getVarName($tableName)."->".getVarNameWithS($tab[$j][0]).");\n";
				}
				$parameterSetter2 = '';
				if(isColumnTypeNumber($tab[$j][1])){
					$parameterSetter2 .= "Number";
				}
				$queryByField .= "	public function queryBy".getClazzName($tab[$j][0])."(\$value){
		\$sql = 'SELECT * FROM ".$tableName." WHERE ".$tab[$j][0]." = ?';
		\$sqlQuery = new SqlQuery(\$sql);
		\$sqlQuery->set".$parameterSetter2."(\$value);
		return \$this->getList(\$sqlQuery);
	}\n\n";
				$deleteByField .= "	public function deleteBy".getClazzName($tab[$j][0])."(\$value){
		\$sql = 'DELETE FROM ".$tableName." WHERE ".$tab[$j][0]." = ?';
		\$sqlQuery = new SqlQuery(\$sql);
		\$sqlQuery->set".$parameterSetter2."(\$value);
		return \$this->executeUpdate(\$sqlQuery);
	}\n\n";
			}
			$readRow .= "\t\t\$".getVarName($tableName)."->".getVarNameWithS($tab[$j][0])." = \$row['".$tab[$j][0]."'];\n";
		}
		if($pk==''){
			continue;
		}
		if(count($pks)==1){
			$template = new Template('templates/DAO.tpl');
			echo '$pk_type '.$pk_type.'<br/>';
			if(isColumnTypeNumber($pk_type)){
				$template->set('pk_number', 'Number');
			}else{
				$template->set('pk_number', '');
			}
		}else{			
			$template = new Template('templates/DAO_with_complex_pk.tpl');
		}
		$template->set('dao_clazz_name', $clazzName );
		$template->set('domain_clazz_name', getDTOName($tableName) );
		$template->set('idao_clazz_name', getClazzName($tableName));
		$template->set('table_name', $tableName);
		$template->set('var_name', getVarName($tableName));
		
		$insertFields = substr($insertFields,0, strlen($insertFields)-2);
		$updateFields = substr($updateFields,0, strlen($updateFields)-2);
		$bindValue = substr($bindValue,0, strlen($bindValue)-2);
		$template->set('pk', $pk);
		$s = '';
		$s2 = '';
		$s3 = '';
		$s4 = '';
		$insertFields2 = $insertFields;
		$bindValue2 = $bindValue;
		for($z=0;$z<count($pks);$z++){
			$bindValue2.=', ?';			
			if($z>0){
				$s.=', ';								
				$s2.=' AND ';
				$s3.= "\t\t";
			}			
			$insertFields2.=', '.$pks[$z];
			$s .= '$'.getVarNameWithS($pks[$z]);
			$s2 .= $pks[$z].' = ? ';
			$s3 .= '$sqlQuery->setNumber($'.getVarNameWithS($pks[$z]).');';			
			$s3 .= "\n";
			$s4 .= "\n\t\t";
			$s4 .= '$sqlQuery->setNumber($'.getVarName($tableName).'->'.getVarNameWithS($pks[$z]).');';
			$s4 .= "\n";
		}
		if($s[0]==',')$s = substr($s,1);
		if($bindValue2[0]==',')$bindValue2= substr($bindValue2,1);
		if($insertFields2[0]==',')$insertFields2= substr($insertFields2,1);
		$template->set('question_marks2', $bindValue2);
		$template->set('insert_fields2', $insertFields2);
		$template->set('pk_set_update', $s4);
		$template->set('pk_set', $s3);		
		$template->set('pk_where', $s2);
		$template->set('pks', $s);
		$template->set('pk_php', getVarNameWithS($pk));		
		$template->set('insert_fields', $insertFields);
		$template->set('read_row', $readRow);
		$template->set('update_fields', $updateFields);
		$template->set('question_marks', $bindValue);
		$template->set('parameter_setter',$parameterSetter);
		$template->set('read_row',$readRow);
		$template->set('date', date("Y-m-d H:i"));
		$template->set('queryByFieldFunctions',$queryByField);		
		$template->set('deleteByFieldFunctions',$deleteByField);	
		$template->write(''.$_POST['nomapp'].'/class/mysql/'.$clazzName.'DAO.class.php');
	}
}

function isColumnTypeNumber($columnType){
	echo $columnType.'<br/>';
	if(strtolower(substr($columnType,0,3))=='int' || strtolower(substr($columnType,0,7))=='tinyint'){
		return true;
	}
	return false;
}

function getnerateIDAOObjects($ret){
	for($i=0;$i<count($ret);$i++){
		if(!doesTableContainPK($ret[$i])){
			continue;
		}
		$tableName = $ret[$i][0];
		$clazzName = getClazzName($tableName);
		$tab = getFields($tableName);
		$parameterSetter = "\n";
		$insertFields = "";
		$updateFields = "";
		$bindValue = "";
		$readRow = "\n";
		$pk = '';
		$pks = array();
		$queryByField = '';
		$deleteByField = '';
		for($j=0;$j<count($tab);$j++){
			if($tab[$j][3]=='PRI'){
				$pk = $tab[$j][0];
				$c = count($pks);
				$pks[$c] = $tab[$j][0];
			}else{
				$insertFields .= $tab[$j][0].", ";
				$updateFields .= $tab[$j][0]." = ?, ";
				$bindValue .= "?, ";
				if(isColumnTypeNumber($tab[$j][1])){
					$parameterSetter .= "\t\t\$sqlQuery->setNumber($".getVarName($tableName)."->".getVarNameWithS($tab[$j][0]).");\n";
				}else{
					$parameterSetter .= "\t\t".'$sqlQuery->set($'.getVarName($tab[$j][0]).');'."\n";
				}
				$queryByField .= "\tpublic function queryBy".getClazzName($tab[$j][0])."(\$value);\n\n";
				$deleteByField .= "\tpublic function deleteBy".getClazzName($tab[$j][0])."(\$value);\n\n";
			}
			$readRow .= "\t\t\$".getVarName($tableName)."->".getVarNameWithS($tab[$j][0])." = \$row['".$tab[$j][0]."'];\n";
		}
		if($pk==''){
			continue;
		}
		
		if(count($pks)==1){
			$template = new Template('templates/IDAO.tpl');
		}else{			
			$template = new Template('templates/IDAO_with_complex_pk.tpl');
		}
		
		$template->set('dao_clazz_name', $clazzName );
		$template->set('table_name', $tableName);
		$template->set('var_name', getVarName($tableName));
		
		$s = '';
		$s2 = '';
		$s3 = '';
		$s4 = '';
		$insertFields2 = $insertFields;
		$bindValue2 = $bindValue;
		for($z=0;$z<count($pks);$z++){
			$bindValue2.=', ?';			
			if($z>0){
				$s.=', ';								
				$s2.=' AND ';
				$s3.= "\t\t";
			}			
			$insertFields2.=', '.getVarNameWithS($pks[$z]);
			$s .= '$'.getVarNameWithS($pks[$z]);
			$s2 .= getVarNameWithS($pks[$z]).' = ? ';
			$s3 .= '$sqlQuery->setNumber('.getVarName($pks[$z]).');';			
			$s3 .= "\n";
			$s4 .= "\n\t\t";
			$s4 .= '$sqlQuery->setNumber($'.getVarName($tableName).'->'.getVarNameWithS($pks[$z]).');';
			$s4 .= "\n";
		}
		$template->set('question_marks2', $bindValue2);
		$template->set('insert_fields2', $insertFields2);
		$template->set('pk_set_update', $s4);
		$template->set('pk_set', $s3);		
		$template->set('pk_where', $s2);
		$template->set('pks', $s);
		
		$insertFields = substr($insertFields,0, strlen($insertFields)-2);
		$updateFields = substr($updateFields,0, strlen($updateFields)-2);
		$bindValue = substr($bindValue,0, strlen($bindValue)-2);
		$template->set('pk', $pk);
		$template->set('insert_fields', $insertFields);
		$template->set('read_row', $readRow);
		$template->set('update_fields', $updateFields);
		$template->set('question_marks', $bindValue);
		$template->set('parameter_setter',$parameterSetter);
		$template->set('read_row',$readRow);
		$template->set('date', date("Y-m-d H:i"));
		$template->set('queryByFieldFunctions',$queryByField);
		$template->set('deleteByFieldFunctions',$deleteByField);		
		$template->write(''.$_POST['nomapp'].'/class/dao/'.$clazzName.'DAO.class.php');
	}
}

function getFields($table){
	$sql = 'DESC '.$table;
	return QueryExecutor::execute(new SqlQuery($sql));
}

function getClazzName($tableName){
	$tableName = strtoupper($tableName[0]).substr($tableName,1);
	for($i=0;$i<strlen($tableName);$i++){
		if($tableName[$i]=='_'){
			$tableName = substr($tableName, 0, $i).strtoupper($tableName[$i+1]).substr($tableName, $i+2);
		}
	}
	return $tableName;
}

function getDTOName($tableName){
	$name = getClazzName($tableName);
	if($name[strlen($name)-1]=='s'){
		$name = substr($name, 0, strlen($name)-1);
	}
	return $name;
}

function getVarName($tableName){
	$tableName = strtolower($tableName[0]).substr($tableName,1);
	for($i=0;$i<strlen($tableName);$i++){
		if($tableName[$i]=='_'){
			$tableName = substr($tableName, 0, $i).strtoupper($tableName[$i+1]).substr($tableName, $i+2);
		}
	}
	if($tableName[strlen($tableName)-1]=='s'){
		$tableName = substr($tableName, 0, strlen($tableName)-1);
	}
	return $tableName;
}

function getVarNameWithS($tableName){
	$tableName = strtolower($tableName[0]).substr($tableName,1);
	for($i=0;$i<strlen($tableName);$i++){
		if($tableName[$i]=='_'){
			$tableName = substr($tableName, 0, $i).strtoupper($tableName[$i+1]).substr($tableName, $i+2);
		}
	}
	//if($tableName[strlen($tableName)-1]=='s'){
	//	$tableName = substr($tableName, 0, strlen($tableName)-1);
	//}
	return $tableName;
}

/**
 * Copy a file, or recursively copy a folder and its contents
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @param       int      $permissions New folder creation permissions
 * @return      bool     Returns true on success, false on failure
 */
function xcopy($source, $dest, $permissions = 0755)
{
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest, $permissions);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        xcopy("$source/$entry", "$dest/$entry", $permissions);
    }

    // Clean up
    $dir->close();
    return true;
}

function getRefTableName($tab, $col){
	$sql = 'SELECT REFERENCED_TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE COLUMN_NAME = \''.$col.'\'
AND REFERENCED_COLUMN_NAME IS NOT NULL
AND TABLE_NAME = \''.$tab.'\'';
$var = QueryExecutor::execute(new SqlQuery($sql));
if($var!=null){
	if(count($var)>0)
	return $var[0]['REFERENCED_TABLE_NAME'];
	else return null;
}
else
	return null;
}

function getRefColumnName($tab, $col){
	$sql = 'SELECT REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE COLUMN_NAME = \''.$col.'\'
AND REFERENCED_COLUMN_NAME IS NOT NULL
AND TABLE_NAME = \''.$tab.'\'';
$var = QueryExecutor::execute(new SqlQuery($sql));
	if(count($var)>0)
	return $var[0]['REFERENCED_COLUMN_NAME'];
	else return null;
}

generate();



?>