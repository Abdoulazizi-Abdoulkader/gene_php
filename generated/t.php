<?php
require_once('include_dao.php');
$donnee = DAOFactory::getCaractereDAO()->queryAll();
?>
<table class="table table-bordered table-condensed table-hover" style="margin-top:0px">
<th class="th">Code</th>
<th class="th">Description</th>
<?php
for($i=0;$i<count($donnee);$i++){
$d=$donnee[$i];
?>
<tr>
<td style="font-weight:bold"><?php echo $d->iDCAR ?></td>
<td><?php echo htmlentities($d->lIBCAR) ?></td>
</tr>
<?php 
}
?>

</table>
