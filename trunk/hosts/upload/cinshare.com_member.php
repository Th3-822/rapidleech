<?
// Baking addon !
function GRC($length = 32, $letters = 'abcdef1234567890')
  {
      $s = '';
      $lettersLength = strlen($letters)-1;
     
      for($i = 0 ; $i < $length ; $i++)
      {
      $s .= $letters[rand(0,$lettersLength)];
      }
     
      return $s;
  } 
// End addon
####### Free Account Info. ###########
$cins_login = "";
$cins_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($cins_login & $cins_pass){
	$_REQUEST['login'] = $cins_login;
	$_REQUEST['password'] = $cins_pass;
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
<tr><td nowrap>&nbsp;Login*<td>&nbsp;<input type=text name=login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=password value='' style="width:160px;" />&nbsp;</tr>
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
<div id=login width=100% align=center>Login to cinshare.com</div>
<?php

			$post['submit'] = 1;
			$post['username'] =  $_REQUEST['login'];
			$post['password'] = $_REQUEST['password'];
			$page = geturl("www.cinshare.com", 80, "/user/login", 'http://www.cinshare.com/', 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'cin_temp', 'Error logging in - are your logins correct?');
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode(';',$cookie);
						
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

	
	$ref='http://www.cinshare.com/upload/local';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://www.cinshare.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$rndid = cut_str($page,'var fid = genRandId(',')');
	$uid = cut_str($page,'<input id="uid" type="hidden" value="','"');
	$ID = GRC();
	$servup = cut_str($page,"var curServer = '","'");
	
	$post['Filename']= $lname;
	$post['Upload']= 'Submit Query';
	
	$url=parse_url($servup."/upload/process/".$ID."/".$uid);

	$upagent = "Shockwave Flash";
	$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $post, $lfile, $lname, "Filedata",0,$upagent);
	is_page($upfiles);
	
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	$Url=parse_url('http://www.cinshare.com/upload/getLinks/'.$ID);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://www.cinshare.com/', 0, 0, 0, $_GET["proxy"],$pauth);
	$ddl=cut_str($page,'value="http://www.cinshare.com/videos/watch/','"');
	$download_link= "http://www.cinshare.com/videos/watch/".$ddl;
	$delete_link= "http://www.cinshare.com/files/delete/".$ID;
	}
// Made by Baking 30/07/2009 16:55
// Fixed by Baking 09/11/2009 12:42
?>