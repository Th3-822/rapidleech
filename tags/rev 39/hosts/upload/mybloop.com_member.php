<?php

####### Account Info. ###########
$mybloop_login = ""; //Set you username
$mybloop_pass = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($mybloop_login & $mybloop_pass){
	$_REQUEST['my_login'] = $mybloop_login;
	$_REQUEST['my_pass'] = $mybloop_pass;
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
<tr><td nowrap>&nbsp;Login*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["mybloop.com_member"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to MyBloop</div>
<?php 
	        $post['un'] = $_REQUEST['my_login'];
            $post['pw'] = $_REQUEST['my_pass'];
            $post['rememberme'] = 1;
			
            $page = geturl("www.mybloop.com", 80, "/login/", 0, 0, $post);	
			is_page($page);
			$cookie = GetCookies($page);
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$login_url = "http://www.mybloop.com/members/addfiles.o";
			$Url = parse_url($login_url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match('/"upload".*action="(.*?)"/i', $page, $act);
			preg_match_all('/name="(s|k)".*value="(.*?)"/i', $page, $id);
			$url_action = $act[1];
			$fpost['s'] = $id[2][0];
			$fpost['k'] = $id[2][1];
			$fpost['describe0'] = $lname;
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$login_url, 0, $fpost, $lfile, $lname, "userfile[0]");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			preg_match('/Location: *(.*)/i', $upfiles, $redir);
			$redir = $redir[1];
			$Url = parse_url($redir);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			preg_match('/valign="top"><a href="(.*?)"/i', $page, $flink);
			$download_link = $flink[1];
	}
?>