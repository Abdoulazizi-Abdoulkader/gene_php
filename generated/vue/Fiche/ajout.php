<?php
/**
 * Class that operate on table 'fiche'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$this->titre = 'Enregistrement des fiche';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-xs-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">fiche : Enregistrement
</div>
</div>     
<div class="panel-body" >
<form name="form" class="form-horizontal" action="fiche/enregistrer" method="POST">
<br/>

<div class="form-group"> 
<label for="iDFIC" class="col-sm-4 control-label">iDFIC :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDFIC" name="iDFIC" required placeholder="iDFIC"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDCAR" class="col-sm-4 control-label">iDCAR :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDCAR" name="iDCAR" required placeholder="iDCAR"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDCON" class="col-sm-4 control-label">iDCON :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDCON" name="iDCON" required placeholder="iDCON"> 
</div> 
</div>
<div class="form-group"> 
<label for="obser" class="col-sm-4 control-label">obser :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="obser" name="obser" required placeholder="obser"> 
</div> 
</div>
<br/>
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-8">
						<a href="fiche/" class="btn btn-default btn-primary pull-right">
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
