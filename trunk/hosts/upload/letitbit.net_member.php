<?php

####### Account Info. ###########
$letitbit_net_login = ""; //Set you username
$letitbit_net_pass = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($letitbit_net_login & $letitbit_net_pass){
	$_REQUEST['my_login'] = $letitbit_net_login;
	$_REQUEST['my_pass'] = $letitbit_net_pass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["!letitbit.net_member"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Letitbit.net</div>
<?php 
	        $post['login'] = $_REQUEST['my_login'];
            $post['password'] = $_REQUEST['my_pass'];
			$post['act'] = "login";
			
            $page = geturl("letitbit.net", 80, "/", 0, 0, $post);
			is_page($page);
			
			$cookie = preg_replace("/((log)|(pas)|(host))=deleted;/", "", GetCookies($page)); //sometimes cookies like "log=deleted" appears
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$url_id = 'http://wm.letitbit.net/wm-panel/Upload';
			$Url = parse_url($url_id);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match('/name="MAX_FILE_SIZE" value="(.*)"/i', $page, $max);
			preg_match('/name="owner" type="hidden" value="(.*)"/i', $page, $owner);
			preg_match('/name="pin" type="hidden" value="(.*)"/i', $page, $pin);
			preg_match('/name="base" type="hidden" value="(.*)"/i', $page, $base);
			preg_match('/name="host" type="hidden" value="(.*)"/i', $page, $host);
			preg_match('/name="source" type="hidden" value="(.*)"/i', $page, $source);
			preg_match("/ACUPL_UPLOAD_SERVER = '(.*?)'/i", $page, $ACUPL_UPLOAD_SERVER);

			function randomString($length)
			{
				$random= "";
				srand((double)microtime()*1000000);
				$char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
				$char_list .= "abcdefghijklmnopqrstuvwxyz";
				$char_list .= "1234567890";
	
				for($i = 0; $i < $length; $i++)
				{
					$random .= substr($char_list,(rand()%(strlen($char_list))), 1);
				}
				return $random;
			}
			$acupl_UID = strtoupper(dechex((int)(microtime(true) * 1000))) . '_' . randomString(40);
			$url_action = 'http://' . $ACUPL_UPLOAD_SERVER[1] . '/marker=' . $acupl_UID;
			$fpost['MAX_FILE_SIZE'] = $max[1];
			$fpost['owner'] = $owner[1];
			$fpost['pin'] = $pin[1];
			$fpost['base'] = $base[1];
			$fpost['host'] = $host[1];
			$fpost['source'] = $source[1];
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),0, $cookie, $fpost, $lfile, $lname, "file0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			$Url = parse_url("http://letitbit.net/acupl_proxy.php?srv=". $ACUPL_UPLOAD_SERVER[1] ."&uid=". $acupl_UID);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
			preg_match('/"post_result": "(.*?)"/i', $page, $post_result);
			$Url = parse_url(preg_replace("/letitbit.net/", "vip-file.com", $post_result[1]));
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, "", 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match_all('/textarea.*?>(.*?)</i', preg_replace("/vip-file.com/", "letitbit.net", $page), $links);
			$download_link = $links[1][0];
			$adm_link = preg_replace("/letitbit.net/", "vip-file.com", $links[1][0]);
			$delete_link = $links[1][1];
			echo "<h3><font color='green'>File successfully uploaded to your account</font></h3>";
	}
/*************************\
WRITTEN by kaox 08/05/2009
UPDATE by kaox 05/09/2009
\*************************/
?>