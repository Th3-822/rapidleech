<?php
/* ===================================
   Upload plugin: hostingcup.com
   ===================================
   Author: thangbom40000 @ Share4u.vn
   Date: 2010.11.29
====================================== */

// Default Account Info
$hostingcup_login = ""; //  Set you username
$hostingcup_pass = ""; //  Set your password
//---------------------

$not_done=true;
$continue_up=false;
if ($hostingcup_login & $hostingcup_pass){
	$_REQUEST['my_login'] = $hostingcup_login;
	$_REQUEST['my_pass'] = $hostingcup_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;Email*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["hostingcup.com"]; ?></b></small></tr>
</form>
</table>


<?php
	}

if ($continue_up)
{
	$not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Connecting to hostingcup.com</div>
<?php
	$Url=parse_url("http://www.hostingcup.com/");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	$cookies=GetCookies($page);

	if (empty($_REQUEST['my_login']) || empty($_REQUEST['my_pass'])) html_error('No entered Login/Password');
	$Url=parse_url("http://www.hostingcup.com/");
	$post["op"] = "login";
	$post["redirect"] = "";
	$post["login"]=trim($_REQUEST['my_login']);
	$post["password"]=trim($_REQUEST['my_pass']);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://www.hostingcup.com/", $cookies, $post, 0, $_GET["proxy"],$pauth);
	is_page($page);

	if(strpos($page,"login=") !== false)
		$cookies=$cookies . '; ' . GetCookies($page);
	else
		html_error("Login fail. Username/Password is incorrect!");

	$Url=parse_url("http://www.hostingcup.com/");
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://www.hostingcup.com/", $cookies, 0, 0, $_GET["proxy"],$pauth);
	$cookies=GetCookies($page);

	unset($post);	
	$matc = array();
	preg_match('/name="sess_id" value="(.*)"/i',$page,$matc);
		$post["sess_id"] = $matc[1];
	preg_match('/name="srv_tmp_url" value="(.*)"/i',$page,$matc);
		$post["srv_tmp_url"] = $srv_tmp_url;
	preg_match('/action="(.*)" method="post"/i',$page,$matc);
		$uploadSever = $matc[1];

	$post["tos"] = "1";
	$post["submit_btn"] = " Upload! ";

	//UploadID
	$utype = "reg";
	$upload_type = "file";
	$UID = '';
	for($i=0;$i<12;$i++)
		$UID .= '' . rand(0, 9);
		
	$url=parse_url($uploadSever.$UID."&js_on=1"."&utype=".$utype."&upload_type=".$upload_type);
?>   
<table width=600 align=center>
</td></tr>
<tr><td align=center>
	<script type="text/javascript">document.getElementById('info').style.innerHTML='Uploading...';</script>
<?php
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://www.hostingcup.com/', $cookies, $post, $lfile, $lname, "file_1", "", $_GET["proxy"],$pauth);
    is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	preg_match("/<textarea name='fn'>(.*)<\/textarea><textarea name='st'>(.*)<\/textarea>/i",$upfiles,$matc);
	if (isset($matc[1]))
		$download_link = "http://www.hostingcup.com/". $matc[1] . ".html";
	else
		html_error("Have an error when retriving upload link!");
}
?>