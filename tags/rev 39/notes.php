<html>
<head>
<meta http-equiv="Content-Type"
	content="text/html; charset=windows-1251">
<title>Notes.txt</title>
<style type="text/css">
<!--
@import url("images/rl_style_pm.css");
-->
</style>
</head>
<body>
<center><img src="images/logo_pm.gif" alt="RAPIDLEECH PLUGMOD"><br>
<br>
<?php

	if (!file_exists("files/notes.txt")) {
		$temp = fopen("files/notes.txt","w");
		fclose($temp);
	}
	if ($_POST['notes']) {
		file_put_contents("files/notes.txt",$_POST['notes']);
?>
	<p>File successfully saved!</p>
<?php
	}
	$content = file_get_contents("files/notes.txt");
?>
<form method="post">
<textarea style="width: 70%; height: 300px" name="notes"><?php echo $content; ?></textarea>
<br /><input type="submit" name="submit" value="Save Notes" />
</form>
</center>
</body>
</html>