<?php
//szalinski 14-Jan-09

##BlipTV Login Details## Add your BlipTV logins here
$blip_tv_login = '';
$blip_tv_password = '';



///// DO NOT TOUCH /////
if ($_POST['action'] == 'upload_go')  {   foreach ($_POST as $key => $val)   {    if (empty($val))    {     echo "<center>Form input field <b> $key </b> was left empty! Please make sure to fill out the form completely!</center><br />";     $process = false;    }   }     if ($process === false)   {    echo "<br /><br /><center>Click <a href='javascript:history.back()'>here</a> to go back</center><br />";    exit;   }   ?>
	<div id=info width=100% align=center>Retrive upload ID</div>
	<?   if (empty($blip_tv_login) || empty($blip_tv_password)) html_error('No BlipTV Login Details specified. You can add them in the video.tamtay.vn.php');   $post_url = 'http://uploads.blip.tv/';   $Url = parse_url($post_url);      $post = array();   $post['userlogin'] = $blip_tv_login;   $post['password'] = $blip_tv_password;      $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"],$pauth);       if (!preg_match("/http\:\/\/uploads.blip.tv\/\?form_cookie=(.*)\" enctype/", $page, $matches)) html_error('Error retrieving upload form');        $auth_cookie = cut_str($page, 'Set-Cookie: ', ';');        $action_url = 'http://uploads.blip.tv/?form_cookie=' . trim($matches[1]);       $Url = parse_url($action_url);     $post = array();   $post['title'] = $_POST['title'];   $post['description'] = $_POST['description'];   $post['license'] = $_POST['license'];   $post['interactive_post'] = 1;   $post['categories_id'] = -1;   $post['content_rating'] = -1;   $post['ingest_method'] = 'web';   $post['nsfw'] = 0;   $post['language_code'] = 'en';   $post['language_name'] = 'English';   $post['post'] = 'Upload!';      ?>
	<script>document.getElementById('info').style.display='none';</script>
	<table width=600 align=center>
	</td></tr>
	<tr><td align=center>
	<?php   $not_done = false;   $upfiles = upfile($Url["host"],$Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $action_url, $auth_cookie, $post, $lfile, $lname, "file");     is_page($upfiles);   is_notpresent($upfiles, 'has been successfully posted', 'Upload failed - Unknown Error.');   if(!preg_match("/Your file called <a href=\"(http\:\/\/blip.tv\/file\/[0-9]{7})\">.*<\/a>/", $upfiles, $downurl)) html_error('Error retreving video link. Please check your video account area.');   $download_link = $downurl[1];   ?>
	<script>document.getElementById('progressblock').style.display='none';</script>
<?  }  else  {   $not_done = true;  ?>
	<form method="POST">
	<table>
	<input type="hidden" name="action" value='upload_go'>
		<tr><td colspan=2><h2 align='center'>Blip-TV Upload Info - Please fill out the forms completely</h2><br /><br /></td></tr>
		<tr>
			<th valign='top' width="110">Title</th>
			<td>
				<input type="text" name="title" id="title" size="60" maxlength="255" value="" />
			</td>
		</tr>
		
		<tr>
			<th>Description</th>
			<td>
				<textarea id="description" name="description" style="margin: 0; width: 250px; height: 175px;"></textarea>
			</td>
		</tr>
	<tr>
		<th valign='top'>License</th>
		<td>
	<select name="license" id="license">
		<option value="-1" >No license (All rights reserved)</option>
		<option value="1" >Creative Commons Attribution</option>
		<option value="2" >Creative Commons Attribution-NoDerivs</option>
		<option value="3" >Creative Commons Attribution-NonCommercial-NoDerivs</option>
		<option value="4" >Creative Commons Attribution-NonCommercial</option>
		<option value="5" >Creative Commons Attribution-NonCommercial-ShareAlike</option>
		<option value="6" >Creative Commons Attribution-ShareAlike</option>
		<option value="7" >Public Domain</option>
	</select>
	
	<p><strong>No license (All rights reserved)</strong></p>
	<p>By not selecting a license you retain all rights to your media granted by law.</p><p>You may want to consider choosing a Creative Commons license to allow more liberal use and sharing of your media, though. <br />There are lots of good reasons to choose a Creative Commons license &#151; not the least of which is that doing so helps enrich the world we live in. Check out <a href="http://creativecommons.org/" target="_BLANK">CreativeCommons.org</a> for more information.</p>
	</td></tr>
	<tr>
		<td colspan="2" align="center" style="padding-top:10px;">
		<input type="submit" value="Upload Video" />
		</td>
	</tr>
	</table>
	</form>
<?
}