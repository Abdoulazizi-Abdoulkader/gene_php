<?php
/**
 * Class that operate on table 'profile'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$this->titre = 'Enregistrement des profile';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
<div id="milieu">
<div class="col-xs-offset-3 col-md-6">
<div class="panel panel-primary panel-default" >
<div class="panel-heading">
<div class="panel-title text-center">profile : Enregistrement
</div>
</div>     
<div class="panel-body" >
<form name="form" class="form-horizontal" action="profile/enregistrer" method="POST">
<br/>

<div class="form-group"> 
<label for="iDPRO" class="col-sm-4 control-label">iDPRO :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="iDPRO" name="iDPRO" required placeholder="iDPRO"> 
</div> 
</div>
<div class="form-group"> 
<label for="nOMPRO" class="col-sm-4 control-label">nOMPRO :</label>
<div class="col-sm-6">
<input type="text" class="form-control" id="nOMPRO" name="nOMPRO" required placeholder="nOMPRO"> 
</div> 
</div>
<br/>
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-8">
						<a href="profile/" class="btn btn-default btn-primary pull-right">
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
