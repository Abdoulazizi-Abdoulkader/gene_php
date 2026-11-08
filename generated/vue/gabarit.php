<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"[]>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	        <base href="<?php echo $racineWeb; ?>" >
    <title><?php echo $titre;?></title>
<link href="Contenu/css/bootstrap.css" rel="stylesheet" media="screen">
<link href="Contenu/css/font-awesome.min.css" rel="stylesheet" media="screen">
<link href="Contenu/css/bootstrap-theme.css" rel="stylesheet" media="screen">
<link href="Contenu/css/bootstrap-responsive.css" rel="stylesheet" media="screen">
<link rel="stylesheet" href="Contenu/css/style.css" type="text/css" media="screen"/>
<?php echo $feuilleDeStyle; ?>
</head>
<body>

<?php echo $contenu;?>

    <script type="text/javascript" src="Contenu/js/jquery.js"></script>
    <script type="text/javascript" src="Contenu/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="Contenu/js/script.js"></script>
	<script type="text/javascript" src="Contenu/js/scriptAjax.js"></script>
<?php echo $scriptJS;?>
	</body>

</html>