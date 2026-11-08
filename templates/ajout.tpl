<?php
/**
 * Class that operate on table '${table_name}'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: ${date}
 */

$this->titre = 'Enregistrement des ${table_name}';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-md-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">Saisie et enregistrement des informations
</div>
</div>     
<div class="panel-body">
<form name="form" class="form-horizontal" action="${table_name}/enregistrer" method="POST">
<div class="col-md-12">

<?php if (isset($msgErreur) and isset($erreurType)): ?>
<div style="text-align:center" id="erreur" class="col-sm-12 label label-<?php echo $erreurType; ?>"><?php echo $msgErreur; ?></div>                   
<?php endif; ?>
${content}

<!-- Button -->
<div id="div-btn-frmAjout" class="col-sm-12">
	<button type="submit" class="btn btn-default btn-success" style="margin-right:5px">
		<span class="btn-label"><i class="icon-ok-circle"></i></span> Valider
	</button>
	<a href="${table_name}/" class="btn btn-default btn-danger">
		<span class="btn-label"><i class="icon-remove-circle"></i></span> Annuler
	</a>
</div>
</div>

</form>     
</div>     
</div>  
</div>
</div>
<?php
echo $copyright;
?>
