<?php

require_once('rl_init.php');
if ($options['notes_disable']) {
	require_once('deny.php');
	exit();
}

login_check();

include(TEMPLATE_DIR.'header.php'); ?>
<br />
<?php

	if (!file_exists(DOWNLOAD_DIR.basename(lang(327)).'.txt')) {
		$temp = fopen(DOWNLOAD_DIR.basename(lang(327)).'.txt', 'w');
		fclose($temp);
	}
	if (isset($_POST['notes']) && $_POST['notes']) {
		file_put_contents(DOWNLOAD_DIR.basename(lang(327)).'.txt', $_POST['notes']);
?>
	<p><?php echo lang(325); ?></p>
<?php
	}
	$content = file_get_contents(DOWNLOAD_DIR.basename(lang(327)).'.txt');
?>
<div align="center">
<form method="post" action="<?php echo $PHP_SELF;?>">
<textarea class="notes" name="notes" rows="1" cols="1"><?php echo htmlentities($content); ?></textarea>
<br /><input type="submit" name="submit" value="<?php echo lang(326); ?>" />
</form>
</div>
<?php include(TEMPLATE_DIR.'footer.php'); ?>