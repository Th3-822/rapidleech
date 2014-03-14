<?php
// You can do some initialization for the template here
@date_default_timezone_set(date_default_timezone_get());
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link title="Rapidleech Style" href="templates/plugmod/styles/rl_style_pm.css" rel="stylesheet" type="text/css" />

<title><?php
if (!isset($nn)) $nn = "\r\n";
if (!isset($page_title)) {
	echo 'Rapidleech v2 rev. '.$rev_num;
} else {
	echo htmlspecialchars($page_title);
}
?></title>
<script type="text/javascript">
/* <![CDATA[ */
var php_js_strings = [];
php_js_strings[87] = " <?php echo lang(87); ?>";
php_js_strings[281] = "<?php echo lang(281); ?>";
pic1= new Image(); 
pic1.src="templates/plugmod/images/ajax-loading.gif";
/* ]]> */
</script>
<script type="text/javascript" src="classes/js.js"></script>
<?php
if ($options['ajax_refresh']) { echo '<script type="text/javascript" src="classes/ajax_refresh.js"></script>'.$nn; }
if ($options['flist_sort']) { echo '<script type="text/javascript" src="classes/sorttable.js"></script>'.$nn; }
?>

</head>

<body>
<center><img src="templates/plugmod/images/logo_pm.gif" alt="RapidLeech PlugMod" border="0" /></center><br />