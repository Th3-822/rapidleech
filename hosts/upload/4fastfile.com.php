<?php 

####### Free Account Info. ###########
$ffasfile_username = ""; //  Set you username
$ffasfile_password = ""; //  Set your password
##############################

$not_done = TRUE;
$continue_up = FALSE;

if ($ffasfile_username && $ffasfile_password) {
	$_REQUEST['my_login'] = $ffasfile_username;
	$_REQUEST['my_pass'] = $ffasfile_password;
}

if (!isset($_REQUEST['my_login']) || !isset($_REQUEST['my_pass'])) {

?>

<form method="post">
<table align="center">
<tr><td>Username*</td><td>: <input type="text" name="my_login" /></td></tr>
<tr><td>Password*</td><td>: <input type="password" name="my_pass" /></td></tr>
<tr><td colspan="2"><input type="submit" value="Upload" /></td></tr>
<tr><td colspan="2"><small>*You can set default login info in 4fasfile.com.php</small></td></tr>
</table>
</form>

<?php

}
else {

?>

<div style="width: 600px; margin: 0 auto; text-align: center;">
<div id="info">Preparing upload...</div>

<?php

  $continue_up = TRUE;
  $not_done = FALSE;

  // Get authentication cookie.
  $ref = 'http://4fastfile.com/';
  $in = parse_url($ref . 'user/login');

  $post = array();
  $post['name'] = $_REQUEST['my_login'];
  $post['pass'] = $_REQUEST['my_pass'];
  $post['form_id'] = 'user_login';

  $page = geturl($in['host'], 80, $in['path'], $ref, 0, $post, 0, $_GET['proxy'], $pauth);
  is_page($page);

  if (!strpos($page, 'Location: ' . $ref) || !preg_match_all('/SESS\w{32}=\w{32}/', $page, $matches)) {
    html_error('Login failed: Bad username/password combination.');
  }

  $cookie = array_pop($matches[0]);

  // Prepare upload parameters
  $in = parse_url($ref . 'imce');

  $page = geturl($in['host'], 80, $in['path'], $ref, $cookie, $post, 0, $_GET['proxy'], $pauth);
  is_page($page);

  $post = array();
  $post['form_token'] = cut_str($page, 'id="edit-imce-upload-form-form-token" value="', '"');
  if (strlen($post['form_token']) != 32) {
    html_error('Unable to parse upload form.');
  }
  $post['form_id'] = 'imce_upload_form';

  $qdir = cut_str($page, ', "dir": "', '"') == '.' && ($qfsdir = cut_str($page, 'link_dir": "', '"')) ? "?dir=$qfsdir" : '';

?>

<script type="text/javascript">document.getElementById("info").style.display = "none";</script>

<?php

  // Upload file
  $page = upfile($in['host'], 80, $in['path'] . $qdir, $ref, $cookie, $post, $lfile, $lname, 'files[imce]');
  is_page($page);

?>

<script type="text/javascript">document.getElementById("progressblock").style.display = "none";</script>

<?php

  // Parse the result and get file URL
  $messages = cut_str($page, '<div class="messages status">', '</div>');
  $rpos = strpos($messages, '</em> has been uploaded');

  if (!$rpos) {
    html_error('Upload failed:' . cut_str($page, '<div class="messages error">', '</div>'));
  }

  $lpos = strrpos(substr($messages, 0, $rpos), '<em>') + 4;
  $fname = substr($messages, $lpos, $rpos - $lpos);

  if (!$fname || !strpos($page, 'id="'. $fname .'"') || ($fname = rawurlencode($fname)) && !strpos($page, 'id="'. $fname .'"')) {
    html_error('Unable to get uploaded file info.');
  }

  $udir = cut_str($page, ', "dir": "', '"');
  $fsdir = cut_str($page, ', "genlink_dir": "', '"');

  if (strpos($udir . '/', $fsdir . '/') === 0) {
    $furl = cut_str($page, ', "genlink_url": "', '"') . substr($udir, strlen($fsdir)) . '/' . $fname;
  }
  else {
    $furl = cut_str($page, ', "furl": "', '"') . ($udir == '.' ? '' : $udir . '/') . $fname;
  }

  $download_link = $furl;
}

?>