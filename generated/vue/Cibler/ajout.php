<?php
/**
 * Class that operate on table 'cibler'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$this->titre = 'Enregistrement des cibler';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-xs-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">cibler : Enregistrement
</div>
</div>     
<div class="panel-body" >
<form name="form" class="form-horizontal" action="cibler/enregistrer" method="POST">
<br/>

<div class="form-group"> 
<label for="iDSER" class="col-sm-4 control-label">iDSER :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDSER" name="iDSER" required placeholder="iDSER"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDFIC" class="col-sm-4 control-label">iDFIC :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDFIC" name="iDFIC" required placeholder="iDFIC"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDCIB" class="col-sm-4 control-label">iDCIB :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDCIB" name="iDCIB" required placeholder="iDCIB"> 
</div> 
</div>
<div class="form-group"> 
<label for="archive" class="col-sm-4 control-label">archive :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="archive" name="archive" required placeholder="archive"> 
</div> 
</div>
<div class="form-group"> 
<label for="vue" class="col-sm-4 control-label">vue :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="vue" name="vue" required placeholder="vue"> 
</div> 
</div>
<br/>
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-8">
						<a href="cibler/" class="btn btn-default btn-primary pull-right">
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
