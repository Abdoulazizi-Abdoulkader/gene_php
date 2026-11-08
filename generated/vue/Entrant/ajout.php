<?php
/**
 * Class that operate on table 'entrant'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$this->titre = 'Enregistrement des entrant';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-xs-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">entrant : Enregistrement
</div>
</div>     
<div class="panel-body" >
<form name="form" class="form-horizontal" action="entrant/enregistrer" method="POST">
<br/>

<div class="form-group"> 
<label for="iDENT" class="col-sm-4 control-label">iDENT :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDENT" name="iDENT" required placeholder="iDENT"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDCOU" class="col-sm-4 control-label">iDCOU :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDCOU" name="iDCOU" required placeholder="iDCOU"> 
</div> 
</div>
<div class="form-group"> 
<label for="dATCOU" class="col-sm-4 control-label">dATCOU :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="dATCOU" name="dATCOU" required placeholder="dATCOU"> 
</div> 
</div>
<div class="form-group"> 
<label for="numOrd" class="col-sm-4 control-label">numOrd :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="numOrd" name="numOrd" required placeholder="numOrd"> 
</div> 
</div>
<br/>
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-8">
						<a href="entrant/" class="btn btn-default btn-primary pull-right">
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
