<?php
// You can do some initialization for the template here
?>
<!DOCTYPE html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link title="Rapidleech Style" href="templates/plugmod/styles/rl_style_pm.css" rel="stylesheet" type="text/css">

<title><?php
if (!isset($page_title)) {
	echo 'Rapidleech v2 rev. '.$rev_num;
} else {
	echo htmlentities($page_title);
}
?></title>
<script type="text/javascript" src="classes/js.php"></script>
<script type="text/javascript">
pic1= new Image(); 
pic1.src="templates/plugmod/images/ajax-loading.gif"; 
</script>

</head>

<body>
<center><img src="templates/plugmod/images/logo_pm.gif" alt="RapidLeech PlugMod" border="0"></center><br>