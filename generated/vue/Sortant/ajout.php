<?php
/**
 * Class that operate on table 'sortant'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$this->titre = 'Enregistrement des sortant';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-xs-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">sortant : Enregistrement
</div>
</div>     
<div class="panel-body" >
<form name="form" class="form-horizontal" action="sortant/enregistrer" method="POST">
<br/>

<div class="form-group"> 
<label for="iDSOR" class="col-sm-4 control-label">iDSOR :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDSOR" name="iDSOR" required placeholder="iDSOR"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDCOU" class="col-sm-4 control-label">iDCOU :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDCOU" name="iDCOU" required placeholder="iDCOU"> 
</div> 
</div>
<div class="form-group"> 
<label for="dES" class="col-sm-4 control-label">dES :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="dES" name="dES" required placeholder="dES"> 
</div> 
</div>
<br/>
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-8">
						<a href="sortant/" class="btn btn-default btn-primary pull-right">
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
