<?php
// addon debug

define('RAPIDLEECH', 'yes');
require_once("http.php");
require_once("other.php");
$nn = "\r\n";

//addon
function BiscottiDiKaox($content)
{
preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
foreach ($matches[1] as $coll) {
$bis0=split(";",$coll);
$bis1=$bis0[0]."; ";
$bis2=split("=",$bis1);
if (substr_count($bis,$bis2[0])>0)
{$patrn=$bis2[0]."[^ ]+";
$bis=preg_replace("/$patrn/",$bis1,$bis);
} else{$bis.=$bis1 ; }}
$bis=str_replace(" "," ",$bis);
return rtrim($bis);}
// end addon

####### Free Account Info. ###########
$wat_login = "";
$wat_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($wat_login & $wat_pass){
	$_REQUEST['username'] = $wat_login;
	$_REQUEST['password'] = $wat_pass;
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
<tr><td nowrap>&nbsp;Login*<td>&nbsp;<input type=text name=username value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=password value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["wat.tv"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to WAT.TV</div>
<?php
			$post['username'] = $_REQUEST['username'];
			$post['password'] = $_REQUEST['password'];
			$page = geturl("www.wat.tv", 80, "/login", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 302 Found', 'Error logging in - are your logins correct?');
			$cookie=BiscottiDiKaox($page);
			$page = geturl("www.wat.tv", 80, "/upload", 0, $cookie, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

			$upload_form = cut_str($page, 'enctype="multipart/form-data" action="', '"');
			$uploaded = cut_str($page, 'enctype="multipart/form-data" action="http://upload.wat.tv', '"');
			if (!$url = parse_url($upload_form)) html_error('Error getting upload url');
			$fpost = array();
			$fpost['send'] = "Envoyer";
?>
<script>document.getElementById('info').style.display='none';</script>
<?php	

			$cookie2 = "wousdat_profil=1"."; $cookie";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $upload_form, 0, $fpost, $lfile, $lname, "file");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php

			$page = geturl("www.wat.tv", 80, "$uploaded.&token=&title=&tags=&genre=&description=&country=&city=&public=&rubid=&chaineid=", 0, $cookie2, 0, 0, "");
			is_page($page);
			preg_match('/Location: (.*)\r\n/i', $page, $infos);
			$download_link = 'http://www.wat.tv'.$infos[1];
	}		
// Made by Baking 08/05/2008
// Thank to Kaox for it's help ^^
?>