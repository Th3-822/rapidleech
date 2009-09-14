<?
####### Free Account Info. ###########
$mftp_login = "";
$mftp_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($mftp_login & $mftp_pass){
	$_REQUEST['email'] = $mftp_login;
	$_REQUEST['password'] = $mftp_pass;
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
<tr><td nowrap>&nbsp;email*<td>&nbsp;<input type=text name=email value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=password value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password Protect<td>&nbsp;<input type=text name=psw value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
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
<div id=login width=100% align=center>Login to megaftp.com</div>
<?php

			$post['uname'] =  urlencode($_REQUEST['email']);
			$post['pwd'] = urlencode($_REQUEST['password']);
			$post['login'] = "Click+Here+To+Login" ;
			$page = geturl("megaftp.com", 80, "/members", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode(';',$cookie);

?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

			$ref='http://www.megaftp.com/';
			$Url=parse_url('http://upload.megaftp.com/old_school.php');
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);

			$post["upload_range"]=cut_str($page,'"upload_range" value="','"');
			$post["day"]=cut_str($page,'"day" value="','"');
			$post["month"]=cut_str($page,'"month" value="','"');
			$post["psw"]= $_REQUEST['psw'];

			$temp = "http://upload.megaftp.com/cgi-bin/uu_ini_status.pl?".cut_str($page,'cgi-bin/uu_ini_status.pl?','"');
			$upurl=cut_str($page,'multipart/form-data"  action="','"');
			if (!$upurl) html_error ('Error get upload url');
			$upurl=$ref.$upurl;
			
			
			$url=parse_url($upurl);
			$upfiles=upfile('upload.megaftp.com',defport($url),	$url["path"].($url["query"]	? "?".$url["query"]	: ""), 'http://upload.megaftp.com/', $cookies, $post, $lfile, $lname, "upfile_0");
			is_page($upfiles);
?>
<script>document.getElementById('info').style.display='none';</script>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php

			is_page($upfiles);

			unset($post);
			$post["tmp_sid"]=cut_str($upfiles,"'tmp_sid' value=\"","\"");
			$post["temp_dir"]=cut_str($upfiles,"'temp_dir' value=\"","\"");
			$final_url=trim(cut_str($upfiles,'action="','"'));
			
			if(!$final_url) html_error("Error get location");

?>
<div id=info2 width=100% align=center><b>Get finaly	file code</b></div>
<?php

			$url=parse_url($final_url);
			$page = geturl($url["host"],defport($url),	$url["path"].($url["query"]	? "?".$url["query"]	: ""), 'http://upload.megaftp.com/', $cookies, $post, 0, "");
			is_page($page);

?>
	<script>document.getElementById('info2').style.display='none';</script>
<?php
echo $page;
			$tmp=cut_str($page,'LINK TO FILE','</tr>');
			$tmp1=cut_str($tmp,'VALUE="http','"');
			
			if (!$tmp1) html_error ('Error get download link');
			
			$tmp2=cut_str($page,'SIZE="40" readonly="0" VALUE="','"></div></td>');
			$tmp3=cut_str($tmp,'VALUE="http://www.MegaFTP.com/','"');
			$download_link='http'.$tmp1;
			$delete_link = 'http://delete.MegaFTP.com/'.$tmp3.'&'.$tmp2;
	}
// sert 15.06.2009
// Member upload plugins by Baking 03/07/2009 14:54
// Fixed by Baking 07/09/2009 08:44
?>