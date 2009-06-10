<?php

####### Account Info. ###########
$ziddu_login = ""; //Set your Email Id
$ziddu_pass = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($ziddu_login & $ziddu_pass){
	$_REQUEST['my_login'] = $ziddu_login;
	$_REQUEST['my_pass'] = $ziddu_pass;
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
<tr><td nowrap>&nbsp;Email Id*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["ziddu.com_member"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Ziddu</div>
<?php
			if (!$_REQUEST['my_login'] || !$_REQUEST['my_pass']) html_error('You didn\'t enter any account login details!');
			$page = geturl("www.ziddu.com", 80, "/login.php", 0, 0, 0);	
			$cookie = GetCookies($page);
			$post['email'] = $_REQUEST['my_login'];
            $post['password'] = $_REQUEST['my_pass'];
            $post['Submit'] = "&nbsp;Login&nbsp;";
			$post['action'] = "LOGIN";
			$post['cookie'] = "";
			$post['uid'] = "";
            $page = geturl("www.ziddu.com", 80, "/login.php", 0, $cookie, $post);	
			is_page($page);
			is_present($page, 'Either Email or Password is Incorrect', 'Error Logging In - Check your login details');
			is_present($page, 'Your Account has been unsubscribed by your self', 'Error Logging In - Check your login details');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$upload_form0 = "http://www.ziddu.com/upload.php";
			$Url = parse_url($upload_form0);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match('/name="mmemid".*value="(.*?)"/i', $page, $mmemid);
			preg_match('/name="mname".*value="(.*?)"/i', $page, $mname);
			$post = array();
			$post['mmemid'] = $mmemid[1];
			$post['mname'] = $mname[1];
			$post['lang'] = 'english';
			$upload_form = 'http://uploads.ziddu.com/upload.php';
			$Url = parse_url($upload_form);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$cookie2 = GetCookies($page);
			preg_match('%action="(cgi-bin/.*?)"%i', $page, $act);
			preg_match('/name="memail".*value="(.*?)"/i', $page, $memail);
			$url_action = 'http://uploads.ziddu.com/'.$act[1];
			$fpost = array();
			$fpost['memail'] = $memail[1];
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$upload_form, $cookie2, $fpost, $lfile, $lname, "upfile_0", "", $upagent);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			preg_match('%ocation:\s(.+)\r\n%', $upfiles, $fredir);
			$Url = parse_url($fredir[1]);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, $post, 0, $_GET["proxy"],$pauth);
			preg_match('%<a href="(http://www.ziddu.com/download/\d+/.+\.html)"\s+%i', $page, $flink);
			$download_link = $flink[1];
	}
	////szal20-03-09
?>