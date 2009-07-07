<?php
// You can do some initialization for the template here
?>
<!DOCTYPE html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link href="templates/plugmod/images/rl_style_pm.css" rel="stylesheet" type="text/css" />

<title><?php
if (!isset($page_title)) {
	echo 'Rapidleech v2 rev. '.$rev_num;
} else {
	echo htmlentities($page_title);
}
?></title>
<script type="text/javascript" src="classes/js.php"></script>
<SCRIPT language="JavaScript">
<!--
pic1= new Image(); 
pic1.src="templates/plugmod/images/ajax-loading.gif"; 
//-->
</SCRIPT>

</head>

<body>
<center><img src="templates/plugmod/images/logo_pm.gif" alt="RapidLeech PlugMod" border="0"></center><br>