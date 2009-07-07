<?php
define('RAPIDLEECH', 'yes');
require('configs/config.php');
define ( 'TEMPLATE_DIR', 'templates/'.$options['template_used'].'/' );
include(TEMPLATE_DIR.'header.php'); ?>
<br>
<?php

	if (!file_exists("files/notes.txt")) {
		$temp = fopen("files/notes.txt","w");
		fclose($temp);
	}
	if (isset($_POST['notes']) && $_POST['notes']) {
		file_put_contents("files/notes.txt",$_POST['notes']);
?>
	<p>File successfully saved!</p>
<?php
	}
	$content = file_get_contents("files/notes.txt");
?>
<div align="center">
<form method="post">
<textarea style="width: 70%; height: 300px" name="notes"><?php echo $content; ?></textarea>
<br /><input type="submit" name="submit" value="Save Notes" />
</form>
</div>
<?php include(TEMPLATE_DIR.'footer.php'); ?>