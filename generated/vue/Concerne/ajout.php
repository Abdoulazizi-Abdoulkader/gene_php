<?php
/**
 * Class that operate on table 'concerne'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$this->titre = 'Enregistrement des concerne';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-xs-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">concerne : Enregistrement
</div>
</div>     
<div class="panel-body" >
<form name="form" class="form-horizontal" action="concerne/enregistrer" method="POST">
<br/>

<div class="form-group"> 
<label for="iDDIR" class="col-sm-4 control-label">iDDIR :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDDIR" name="iDDIR" required placeholder="iDDIR"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDCOU" class="col-sm-4 control-label">iDCOU :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDCOU" name="iDCOU" required placeholder="iDCOU"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDCON" class="col-sm-4 control-label">iDCON :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDCON" name="iDCON" required placeholder="iDCON"> 
</div> 
</div>
<div class="form-group"> 
<label for="archive" class="col-sm-4 control-label">archive :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="archive" name="archive" required placeholder="archive"> 
</div> 
</div>
<div class="form-group"> 
<label for="traite" class="col-sm-4 control-label">traite :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="traite" name="traite" required placeholder="traite"> 
</div> 
</div>
<br/>
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-8">
						<a href="concerne/" class="btn btn-default btn-primary pull-right">
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
