<?php
/**
 * Class that operate on table 'ordonne'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$this->titre = 'Enregistrement des ordonne';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-xs-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">ordonne : Enregistrement
</div>
</div>     
<div class="panel-body" >
<form name="form" class="form-horizontal" action="ordonne/enregistrer" method="POST">
<br/>

<div class="form-group"> 
<label for="iDDIR" class="col-sm-4 control-label">iDDIR :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDDIR" name="iDDIR" required placeholder="iDDIR"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDINS" class="col-sm-4 control-label">iDINS :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDINS" name="iDINS" required placeholder="iDINS"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDORD" class="col-sm-4 control-label">iDORD :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDORD" name="iDORD" required placeholder="iDORD"> 
</div> 
</div>
<br/>
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-8">
						<a href="ordonne/" class="btn btn-default btn-primary pull-right">
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
