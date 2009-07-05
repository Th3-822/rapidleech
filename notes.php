<?php define('RAPIDLEECH', 'yes')?>
<?php require('configs/config.php');?>
<?php include(TEMPLATE_DIR.$options['template_used'].'/header.php'); ?>
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
<?php include(TEMPLATE_DIR.$options['template_used'].'/footer.php'); ?>