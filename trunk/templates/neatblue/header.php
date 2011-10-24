<!DOCTYPE HTML>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="author" content="blacklizt">
<meta name="robots" content="ALL">
<link title="Rapidleech Style" href="templates/neatblue/styles/rl_style_pm.css" rel="stylesheet" type="text/css">
<title><?php
if (!isset($page_title)) {
	echo 'Rapidleech v2 rev. '.$rev_num;
} else {
	echo htmlentities($page_title);
}
?></title>
<script type="text/javascript">
	/* <![CDATA[ */
var php_js_strings = [];
php_js_strings[87] = " <?php echo lang(87); ?>";
php_js_strings[281] = "<?php echo lang(281); ?>";
pic1= new Image(); 
	pic1.src="templates/neatblue/images/ajax-loading.gif";
	/* ]]> */
</script>
<script type="text/javascript" src="classes/js.js"></script>	
<?php
	if ($options['ajax_refresh']) { echo '<script type="text/javascript" src="classes/ajax_refresh.js"></script>'.$nn; }
	if ($options['flist_sort']) { echo '<script type="text/javascript" src="classes/sorttable.js"></script>'.$nn; }
?></head>
<body>
<header id="logo"><img src="templates/neatblue/images/logo_pm.gif" height="62" width="369" alt="RapidLeech Neatblue"></header><br />