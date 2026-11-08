<?php
/**
 * Class that operate on table 'instruction'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$this->titre = 'Enregistrement des instruction';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-xs-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">instruction : Enregistrement
</div>
</div>     
<div class="panel-body" >
<form name="form" class="form-horizontal" action="instruction/enregistrer" method="POST">
<br/>

<div class="form-group"> 
<label for="iDINS" class="col-sm-4 control-label">iDINS :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDINS" name="iDINS" required placeholder="iDINS"> 
</div> 
</div>
<div class="form-group"> 
<label for="lIBINS" class="col-sm-4 control-label">lIBINS :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="lIBINS" name="lIBINS" required placeholder="lIBINS"> 
</div> 
</div>
<br/>
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-8">
						<a href="instruction/" class="btn btn-default btn-primary pull-right">
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
