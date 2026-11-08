<?php
/**
 * Class that operate on table '${table_name}'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: ${date}
 */

$this->titre = 'Liste des ${table_name}';
$this->feuilleDeStyle = '
<link rel="stylesheet" href="Contenu/css/dataTables.bootstrap.css" type="text/css" media="screen" />';
$this->scriptJS = '
<script language="javascript" src="Contenu/js/jquery.dataTables.js" type="text/javascript"></script>
<script language="javascript" src="Contenu/js/dataTables.bootstrap.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
    $(\'#example\').DataTable();
} );
</script>';

echo $menu;

?>
  <div id="milieu">
					 <div class="">
					<a id="btn-ajout" href="${table_name}/ajout" class="btn btn-default btn-warning"><i class="icon-plus-sign"></i> Ins&eacute;rer</a>
					</div>					
					<?php
					if(count($donnee)>0){
		echo '<table id="example" class="table table-bordered table-condensed table-hover" style="margin-top:0px">
		<thead>
		${table_head}';
		
		for($i=0;$i<count($donnee);$i++){
		$d=$donnee[$i];
		echo '${table_content}';	
		
		}
        echo '</tbody></table>';
		}?>
        </div>
<?php
echo $copyright;
?>
