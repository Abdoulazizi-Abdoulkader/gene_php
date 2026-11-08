<?php
/**
 * Class that operate on table 'instruction'. Database Mysql.
 *
 * @author: Mahaman Sani NOMAOU DAN LAMSO
 * @date: 2018-02-27 08:37
 */

$donnee = DAOFactory::getInstructionDAO()->queryAll();
$this->titre = 'Liste des instruction';
$this->feuilleDeStyle = '';
$this->scriptJS = '';

echo $menu;

?>
  <div id="milieu">
        <div class="col-xs-offset-1 col-md-10" style="padding-right:30px;padding-left:0px">
                    <div class="pull-right">
					<a href="instruction/ajout" class="btn btn-default btn-danger" style="margin-bottom:7px"><i class="icon-plus"></i>Ajouter</a>
					</div>
		<table class="table table-bordered table-condensed table-hover" style="margin-top:0px">
				<tr>	<th class="th">iDINS</th>
	<th class="th">lIBINS</th>
	<th class="th last">Actions</th></tr>

				<?php
				for($i=0;$i<count($donnee);$i++){
				$d=$donnee[$i];
				echo '<tr>
<td style="font-weight:bold">'.htmlentities($d->iDINS).'</td>
<td>'.htmlentities($d->lIBINS).'</td>
<td><div class="btn-group"><a href="" class="btn btn-default btn-sm btn-info"><i class="icon-eye-open"></i></a><a href="#<?php echo $d->iDINS ?>" class="btn btn-default btn-sm btn-warning"><i class="icon-pencil"></i></a>
</div></td></tr>';	
				}?>
        </table>
        </div>
</div>
<?php
echo $copyright;
?>
