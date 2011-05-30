<?php

####### Account Info. ###########
$shared4_login = ""; //Set you username
$shared4_pass = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($shared4_login & $shared4_pass){
	$_REQUEST['my_login'] = $shared4_login;
	$_REQUEST['my_pass'] = $shared4_pass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["4shared.com"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to 4shared</div>
<?php 
	        $post['login'] = $_REQUEST['my_login'];
            $post['password'] = $_REQUEST['my_pass'];
			
            $page = geturl("www.4shared.com", 80, "/index.jsp", 0, 0, $post);			
			is_page($page);
			$cookie = GetCookies($page);
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			preg_match('/location *= *"(.*?)"/i', $page, $redir1);
			$redir = $redir1[1];
			$Url = parse_url($redir);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			preg_match('/Location: *(.*)/i', $page, $fmanager);
			$fmanager = $fmanager[1];
			$Url = parse_url($fmanager);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			preg_match('/(http.*upload\.jsp.*?)"/i', $page, $logurl);
			preg_match('/sId=(.*?)"/', $page, $sid);

			$url_action = $logurl[1];
			preg_match('/mainDC.*value="(.*?)"/i', $page, $mainDC);
			$fpost['mainDC'] = $mainDC[1];
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$fmanager, $cookie, $fpost, $lfile, $lname, "fff0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			$dl_page = "http://www.4shared.com/account/changedir.jsp?sId=".$sid[1];
			$Url = parse_url($dl_page);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			$ext_uppedname = strrchr($lname, ".");
			$uppedname = explode($ext_uppedname, $lname);
			if(preg_match('%<a href="(http.*/'.$uppedname[0].'.*\.html)%', $page, $flink)){
			$download_link = $flink[1];
			}else{
				html_error("Finished, Go to your account to see Download-URL.", 0);
			}
	}
?>