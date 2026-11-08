<?php
/**
 * Class that operate on table 'personne'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$this->titre = 'Enregistrement des personne';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-xs-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">personne : Enregistrement
</div>
</div>     
<div class="panel-body" >
<form name="form" class="form-horizontal" action="personne/enregistrer" method="POST">
<br/>

<div class="form-group"> 
<label for="iDPER" class="col-sm-4 control-label">iDPER :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDPER" name="iDPER" required placeholder="iDPER"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDSER" class="col-sm-4 control-label">iDSER :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDSER" name="iDSER" required placeholder="iDSER"> 
</div> 
</div>
<div class="form-group"> 
<label for="nOMPER" class="col-sm-4 control-label">nOMPER :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="nOMPER" name="nOMPER" required placeholder="nOMPER"> 
</div> 
</div>
<div class="form-group"> 
<label for="pRENOM" class="col-sm-4 control-label">pRENOM :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="pRENOM" name="pRENOM" required placeholder="pRENOM"> 
</div> 
</div>
<div class="form-group"> 
<label for="aGE" class="col-sm-4 control-label">aGE :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="aGE" name="aGE" required placeholder="aGE"> 
</div> 
</div>
<div class="form-group"> 
<label for="tEL" class="col-sm-4 control-label">tEL :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="tEL" name="tEL" required placeholder="tEL"> 
</div> 
</div>
<div class="form-group"> 
<label for="mAIL" class="col-sm-4 control-label">mAIL :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="mAIL" name="mAIL" required placeholder="mAIL"> 
</div> 
</div>
<br/>
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-8">
						<a href="personne/" class="btn btn-default btn-primary pull-right">
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
