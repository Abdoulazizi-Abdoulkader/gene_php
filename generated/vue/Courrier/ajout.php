<?php
/**
 * Class that operate on table 'courrier'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$this->titre = 'Enregistrement des courrier';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-xs-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">courrier : Enregistrement
</div>
</div>     
<div class="panel-body" >
<form name="form" class="form-horizontal" action="courrier/enregistrer" method="POST">
<br/>

<div class="form-group"> 
<label for="iDCOU" class="col-sm-4 control-label">iDCOU :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDCOU" name="iDCOU" required placeholder="iDCOU"> 
</div> 
</div>
<div class="form-group"> 
<label for="oBJ" class="col-sm-4 control-label">oBJ :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="oBJ" name="oBJ" required placeholder="oBJ"> 
</div> 
</div>
<div class="form-group"> 
<label for="dATENR" class="col-sm-4 control-label">dATENR :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="dATENR" name="dATENR" required placeholder="dATENR"> 
</div> 
</div>
<div class="form-group"> 
<label for="nBRP" class="col-sm-4 control-label">nBRP :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="nBRP" name="nBRP" required placeholder="nBRP"> 
</div> 
</div>
<div class="form-group"> 
<label for="eXP" class="col-sm-4 control-label">eXP :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="eXP" name="eXP" required placeholder="eXP"> 
</div> 
</div>
<div class="form-group"> 
<label for="oBS" class="col-sm-4 control-label">oBS :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="oBS" name="oBS" required placeholder="oBS"> 
</div> 
</div>
<div class="form-group"> 
<label for="chemin" class="col-sm-4 control-label">chemin :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="chemin" name="chemin" required placeholder="chemin"> 
</div> 
</div>
<br/>
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-8">
						<a href="courrier/" class="btn btn-default btn-primary pull-right">
                <span class="btn-label"><i class="icon-remove"></i></span> Annuler</a>
						<button type="submit" class="btn btn-default btn-primary pull-right" style="margin-right:10px">
                <span class="btn-label"><i class="icon-ok"></i></span> Valider</button>
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
