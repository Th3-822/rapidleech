<?php
$not_done=true;
$continue_up=false;
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:350px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;Username*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Title*<td>&nbsp;<input name="title" style="width:160px;" maxlength="60">&nbsp;</tr>
<tr><td nowrap>&nbsp;Description*<td>&nbsp;<textarea name="description" style="width:160px;"></textarea>&nbsp;</tr>
<tr><td nowrap>&nbsp;Tags*<td>&nbsp;<input name="tags" style="width:160px;" maxlength="120">&nbsp;</tr>
<tr><td nowrap><td><small>Enter one or more tags, separated by spaces.</small></tr>
<tr><td nowrap><td><b>Select one category that best describe your video*:</b></tr>
<tr><td nowrap><td><select name="channel" id="channel">
  <option value="1">Arts &amp; Animations</option>
  <option value="2">Autos &amp; Vehicles</option>
  <option value="23">Comedy</option>
  <option value="24" selected="selected">Entertainment</option>
  <option value="10">Music</option>
  <option value="25">News &amp; Blogs</option>
  <option value="22">People</option>
  <option value="15">Pets &amp; Animals</option>
  <option value="26">Science &amp; Technology</option>
  <option value="17">Sports</option>
  <option value="19">Travel &amp; Places</option>
  <option value="20">Video Games</option>
</select></tr>
<tr><td nowrap><td><small style="color:#FFEE00">* Required Field</small></tr>
<tr><td colspan=2 align=center><input type=submit value='Upload'></tr>
</table>
</form>

<?php
	}

if ($continue_up)
	{
		$not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=login width=100% align=center>Login to Megavideo.com</div>
<?php 
	        $post['nickname'] = $_REQUEST['my_login'];
            $post['password'] = $_REQUEST['my_pass'];
			$post['action'] = "login";
			$post['cnext'] = 'upload';
			$post['snext'] = '';
			$post['touser'] = '';
			$post['user'] = '';
			
			$login_url = 'http://www.megavideo.com/?s=signup&cnext=upload&snext=';
			$Url = parse_url($login_url);		
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
			
			$cookie = GetCookies($page);
			//preg_match('/user=(.*?);/i', $cookies, $cook);
			//$cookie = 'user='.$cook[1];
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$url_id = 'http://www.megavideo.com/?c=upload';
			$ipost['action'] = 'step2';
			$ipost['language'] = '1';
			$ipost['title'] = $_REQUEST['title'];
			$ipost['description'] = $_REQUEST['description'];
			$ipost['tags'] = $_REQUEST['tags'];
			$ipost['channel'] = $_REQUEST['channel'];
			$Url = parse_url($url_id);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $login_url, $cookie, $ipost, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match('/action="(.*?)"\s*id="uploadfrm"/i', $page, $upurl);			
			$url_action = $upurl[1];
			$fpost['language'] = '1';
			$fpost['title'] = $_REQUEST['title'];
			$fpost['message'] = $_REQUEST['description'];
			$fpost['tags'] = $_REQUEST['tags'];
			$fpost['channels'] = $_REQUEST['channel'].';';
			$fpost['private'] = '0';
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($url_action);
			
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://www.megavideo.com/?c=upload", $cookie, $fpost, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			preg_match('/downloadurl = *\'(.*)\';/i', $upfiles, $flink);
			$download_link = $flink[1];
	}
?>