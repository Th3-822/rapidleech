<?php
####### Account Info. ###########
$upload_acc['wrzuta_pl']['user'] = ""; //Set your username
$upload_acc['wrzuta_pl']['pass'] = ""; //Set your password
##############################

$not_done = true;
$continue_up = false;

$fname = substr($lname, 0, strrpos($lname, '.'));
$categories = array(1 => "Other (pozostale)", 2 => "Adult (erotyka)", 3 => "Sport", 4 => "Funny (smieszne)", 5 => "Music (muzyka)", 6 => "Advertising (reklamy)", 8 => "Films and TV (filmy i tv)", 9 => "Games (gry)", 10 => "Animals (zwierzeta)", 11 => "Graphics (grafika)", 12 => "For children (dla dzieci)", 13 => "Fashion and beauty (moda i uroda)", 14 => "Science and Technology (nauka i technika)", 15 => "Stars and Showbiz (gwiazdy i showbiznes)");

if ($upload_acc['wrzuta_pl']['user'] && $upload_acc['wrzuta_pl']['pass']){
	$_REQUEST['login'] = $upload_acc['wrzuta_pl']['user'];
	$_REQUEST['password'] = $upload_acc['wrzuta_pl']['pass'];
	$_REQUEST['action'] = "FORM";
	// Set options for uploading with default login:
	$_REQUEST['up_title'] = "$fname";
	$_REQUEST['up_tags'] = "$fname";
	$_REQUEST['up_description'] = 'Uploaded with Rapidleech';
	$_REQUEST['up_category'] = 1; // Check $categories
	$_REQUEST['up_adult'] = 'no';
	$_REQUEST['up_private'] = 'no';
	$_REQUEST['up_comments'] = 'yes';
	echo "<b><center>Using Default Settings</center></b>\n";
}

if ($_REQUEST['action'] == "FORM")
	$continue_up = true;
else{
?>
<table border="0" style="width:270px;margin:auto;" cellspacing="0">
<form method="POST">
<input type="hidden" name="action" value="FORM" />
<tr><td style="white-space:nowrap;">&nbsp;Username*<td>&nbsp;<input type="text" name="login" value="" style="width:160px;" />&nbsp;</tr>
<tr><td style="white-space:nowrap;">&nbsp;Password*<td>&nbsp;<input type="password" name="password" value="" style="width:160px;" />&nbsp;</tr>
<tr><td colspan='2' align='center'><br />Upload options*<br /><br /></td></tr>
<tr><td style='white-space:nowrap;'>Title:</td><td>&nbsp;<input type='text' name='up_title' value="<?php echo $fname ?>" style='width:160px;' /></td></tr>
<tr><td style='white-space:nowrap;'>Tags:</td><td>&nbsp;<input type='text' name='up_tags' value="<?php echo $fname; ?>" style='width:160px;' /></td></tr>
<tr><td style='white-space:nowrap;'>Description:</td><td>&nbsp;<input type='text' name='up_description' value='Uploaded with Rapidleech' style='width:160px;' /></td></tr>
<tr><td style='white-space:nowrap;'>Category:</td><td>&nbsp;<select name='up_category' style='width:160px;height:20px;'>
<?php foreach($categories as $v => $n) echo "<option value='$v'>$n</option>\n"; ?>
</select></td></tr>
<tr><td colspan='2' align='center'>Content type:<input type='radio' name='up_adult' value='no' checked='checked'>&nbsp;Safe&nbsp;&nbsp;<input type='radio' name='up_adult' value='yes'>&nbsp;<b style="color:red;">Unsafe</b></td></tr>
<tr><td style='white-space:nowrap;'><input type='checkbox' name='up_private' value='yes' />&nbsp; Set as private</td><td style='white-space:nowrap;' align='right'><input type='checkbox' name='up_comments' value='yes' checked='checked' />&nbsp; Allow Comments</td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="Upload" /></td></tr>
<tr><td colspan="2" align="center"><small>*You can set it as default in <b><?php echo $page_upload["wrzuta.pl"]; ?></b></small></td></tr>
</form>
</table>
<script type='text/javascript'>self.resizeTo(700,500);</script>
<?php
}

if ($continue_up)
	{
		$not_done=false;
?>
<table style="width:600px;margin:auto;">
</td></tr>
<tr><td align="center">
<div id="info" style="width:100%;text-align:center;">Login to wrzuta.pl</div>
<?php
	$page = geturl("www.wrzuta.pl", 80, "/ajax/csrf/pobierz/", 'http://www.wrzuta.pl/', 0, 0, 0, $_GET["proxy"], $pauth);is_page($page);
	$cookie = GetCookiesArr($page);
	if (!empty($_REQUEST['login']) && !empty($_REQUEST['password'])) {
		$post = array();
		$post['login_form_login'] = $_REQUEST['login'];
		$post['login_form_password'] = sha1(sha1($_REQUEST['password']).strtolower($_REQUEST['login']));
		$post['login_form_remember'] = 0;
		$post['csrf'] = $cookie['csrf'];

		$page = geturl("www.wrzuta.pl", 80, "/ajax/uzytkownik/zaloguj", 'http://www.wrzuta.pl/', $cookie, $post, 0, $_GET["proxy"], $pauth);is_page($page);
		is_present($page, 'B\u0142\u0119dny login lub has\u0142o', "Login failed: User/Password incorrect.");
		is_present($page, 'Autoryzacja nie powiod\u0142a si\u0119', "Login failed: Auth Failed.");
		is_notpresent($page, '"status":"ok"', 'Login Error?');
		is_notpresent($page, 'Set-Cookie: SID=', 'Error: Cannot find session cookie.');
		$cookie = array_merge($cookie, GetCookiesArr($page));
		$login = true;
	} else {
		echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
		$login = false;
	}
?>
<script type="text/javascript">document.getElementById('info').innerHTML='Uploading...';</script>
<?php
	$post = array();
	$post['Filename'] = $lname;
	$post['Upload'] = 'Submit Query';

	$upfiles = upfile('www.wrzuta.pl', 80, '/ajax/pliki/dodaj', '', 0, $post, $lfile, $lname, "file", '', $_GET["proxy"], $pauth, 'Shockwave Flash');
	is_page($upfiles);
	is_notpresent($page, '"status":"ok"', 'Upload failed?');
	if (!preg_match('@"token":"([^\"]+)"@i', $upfiles, $token)) html_error("Error: File token not found!.", 0);
	$token = $token[1];
?>
<script type="text/javascript">document.getElementById('progressblock').style.display='none';document.getElementById('info').innerHTML='Saving file...';</script>
<?php
	$post = array();
	$post['token0'] = $token;
	$post['files_qnt'] = 1;

	$page = geturl('www.wrzuta.pl', 80, "/ajax/pliki/statusuploadu", 'http://www.wrzuta.pl/', $cookie, $post, 0, $_GET["proxy"], $pauth);is_page($page);
	is_notpresent($page, '"uploaded_cnt":1', 'Cannot check uploaded file');

	$page = geturl('www.wrzuta.pl', 80, "/pliki/edytuj", 'http://www.wrzuta.pl/', $cookie, $post, 0, $_GET["proxy"], $pauth);is_page($page);

	$post = array();
	$post['files_qnt'] = 1;
	$post['files_idxs'] = cut_str($page, 'name="files_idxs" value="', '"');
	$post['sess_id'] = cut_str($page, 'name="MAX_FILE_SIZE" value="', '"');
	$post['csrf'] = cut_str($page, 'name="csrf" value="', '"');
	$post['token0'] = $token;
	$post['key_0'] = cut_str($page, 'name="key_0" value="', '"');
	$post['upload_file_name_0'] = !empty($_REQUEST['up_title']) ? urlencode($_REQUEST['up_title']) : urlencode($fname);
	$post['upload_file_tags_0'] = !empty($_REQUEST['up_tags']) ? urlencode($_REQUEST['up_tags']) : urlencode($fname);
	$post['upload_file_dir_key_0'] = 0;
	$post['upload_file_desc_0'] = !empty($_REQUEST['up_description']) ? urlencode($_REQUEST['up_description']) : 'Uploaded+with+rapidleech';
	$post['id_subcategory_0'] = $post['id_category_0'] = (array_key_exists($_REQUEST['up_category'], $categories)) ? $_REQUEST['up_category'] : 1;
	$post['upload_file_adults_0'] = ($_REQUEST['up_adult'] == 'yes') ? 1 : 0;
	if ($login) $post['upload_file_private_0'] = ($_REQUEST['up_private'] == 'yes') ? 1 : 0;
	$post['upload_file_comments_0'] = ($_REQUEST['up_comments'] == 'yes') ? 1 : 0;
	$post['accept_rules'] = 'on';

	$page = geturl("www.wrzuta.pl", 80, "/pliki/zapisz", 'http://www.wrzuta.pl/pliki/edytuj', $cookie, $post, 0, $_GET["proxy"], $pauth);is_page($page);
?>
<script type="text/javascript">document.getElementById('info').innerHTML='Getting download link...';</script>
<?php
	is_notpresent($page, 'Location: ', 'Redirect not found');
	if (preg_match('@Location: (https?://[^/]+wrzuta.pl/[^\r|\n]+)@i', $page, $lnk)) {
		$download_link = $lnk[1];
	} else {
		html_error("Error: Download link not found.", 0);
	}
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
}

//[06-1-2012] Written by Th3-822.

?>