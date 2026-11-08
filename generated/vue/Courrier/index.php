<?php
/**
 * Class that operate on table 'courrier'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$donnee = DAOFactory::getCourrierDAO()->queryAll();
$this->titre = 'Liste des courrier';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
  <div id="milieu">
        <div class="col-xs-offset-1 col-md-10" style="padding-right:30px;padding-left:0px">
                    <div class="pull-right">
					<a href="courrier/ajout" class="btn btn-default btn-danger" style="margin-bottom:7px"><i class="icon-plus"></i>Ajouter</a>
					</div>
		<table class="table table-bordered table-condensed table-hover" style="margin-top:0px">
				<tr>	<th class="th">iDCOU</th>
	<th class="th">oBJ</th>
	<th class="th">dATENR</th>
	<th class="th">nBRP</th>
	<th class="th">eXP</th>
	<th class="th">oBS</th>
	<th class="th">chemin</th>
	<th class="th last">Actions</th></tr>

				<?php
				for($i=0;$i<count($donnee);$i++){
				$d=$donnee[$i];
				echo '<tr>
<td style="font-weight:bold">'.htmlentities($d->iDCOU).'</td>
<td>'.htmlentities($d->oBJ).'</td>
<td>'.htmlentities($d->dATENR).'</td>
<td>'.htmlentities($d->nBRP).'</td>
<td>'.htmlentities($d->eXP).'</td>
<td>'.htmlentities($d->oBS).'</td>
<td>'.htmlentities($d->chemin).'</td>
<td><div class="btn-group"><a href="" class="btn btn-default btn-sm btn-info"><i class="icon-eye-open"></i></a><a href="#<?php echo $d->iDCOU ?>" class="btn btn-default btn-sm btn-warning"><i class="icon-pencil"></i></a>
</div></td></tr>';	
				}?>
        </table>
        </div>
</div>
<?php
echo $copyright;
?>
