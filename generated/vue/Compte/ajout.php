<?php
/**
 * Class that operate on table 'compte'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$this->titre = 'Enregistrement des compte';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-xs-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">compte : Enregistrement
</div>
</div>     
<div class="panel-body" >
<form name="form" class="form-horizontal" action="compte/enregistrer" method="POST">
<br/>

<div class="form-group"> 
<label for="iDUSE" class="col-sm-4 control-label">iDUSE :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDUSE" name="iDUSE" required placeholder="iDUSE"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDPER" class="col-sm-4 control-label">iDPER :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDPER" name="iDPER" required placeholder="iDPER"> 
</div> 
</div>
<div class="form-group"> 
<label for="lOGIN" class="col-sm-4 control-label">lOGIN :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="lOGIN" name="lOGIN" required placeholder="lOGIN"> 
</div> 
</div>
<div class="form-group"> 
<label for="pWD" class="col-sm-4 control-label">pWD :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="pWD" name="pWD" required placeholder="pWD"> 
</div> 
</div>
<div class="form-group"> 
<label for="iDPRO" class="col-sm-4 control-label">iDPRO :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDPRO" name="iDPRO" required placeholder="iDPRO"> 
</div> 
</div>
<br/>
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-8">
						<a href="compte/" class="btn btn-default btn-primary pull-right">
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
