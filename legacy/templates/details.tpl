<?php
/**
 * Class that operate on table '${table_name}'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: ${date}
 */

$this->titre = 'Modification des ${table_name}';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-md-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">Détails des informations
</div>
</div>     
<div class="panel-body well">
<form name="form" class="form-horizontal" action="${table_name}/modification" method="POST">
<div class="col-md-12" style="margin-top:5px;">
<?php if (isset($msgErreur) and isset($erreurType)): ?>
<div style="text-align:center" id="erreur" class="col-sm-12 label label-<?php echo $erreurType; ?>"><?php echo $msgErreur; ?></div>                   
<?php endif; ?>
${content}

<input type="hidden" class="hidden" id="id"  name="id" value="<?php echo $id; ?>"/>
<!-- Button -->
<div class="col-sm-12" style="text-align:center; margin-top:10px;margin-bottom:5px">
<button type="submit" value="btn_edit" id="btn" name="btn" class="btn btn-default btn-warning" style="margin-right:3px">
<span class="btn-label"><i class="icon-edit"></i></span> Modifier</button>
<a href="${table_name}/" class="btn btn-default btn-warning">
<span class="btn-label"><i class="icon-remove"></i></span> Fermer</a>
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
