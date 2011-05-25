<?php
####### Account Info. ###########
$hellshare_login = "";
$hellshare_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($hellshare_login & $hellshare_pass){
	$_REQUEST['bin_login'] = $hellshare_login;
	$_REQUEST['bin_pass'] = $hellshare_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Let it empty for free user</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=1 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM'><input type=hidden value=uploaded value'<?php $_REQUEST[uploaded]?>'>
<input type=hidden name=filename value='<?php echo base64_encode($_REQUEST[filename]); ?>'>
<tr><td nowrap>&nbsp;Login<td>&nbsp;<input name=bin_login value='' style="width:160px;">&nbsp;</tr>
<tr><td nowrap>&nbsp;Password<td>&nbsp;<input name=bin_pass value='' style="width:160px;">&nbsp;</tr>
<tr><td colspan=2 align=center>Let it empty for free user</tr>
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
<div id=login width=100% align=center></div> 
<?php
			$Url=parse_url('http://www.hellshare.com/login?do=loginForm-submit');
			if ($_REQUEST['action'] == "FORM")
			{
				$post["username"]=$_REQUEST['bin_login'];
				$post["password"]=$_REQUEST['bin_pass'];
				$post["login"]="Log+in";
			
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.hellshare.com/", 0, $post, 0, $_GET["proxy"], $pauth);
			$cookie1 = "PHPSESSID=".cut_str($page,'Set-Cookie: PHPSESSID=',";")."; ";
			$cookie2 = "nette-browser=".cut_str($page,'Set-Cookie: nette-browser=',";");
			$cookies = "$cookie1$cookie2";
			}	
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$Url=parse_url('http://www.hellshare.com/');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.hellshare.com/", $cookies, 0, 0, $_GET["proxy"], $pauth);
			
			$action_path = cut_str($page,'target="form_upload_0_iframe" method="post" action="','"');
			
			/**
			preg_match('#([0-9]+)\/([a-z0-9]+)#', $action_path, $rnd);
			$rndid = '<script type="text/javascript">
			var ranNum= Math.random();
			document.write (ranNum)</script>';
			$Url= 'http://www.hellshare.com/uu_ini_status_pro.php?tmp_sid='.$rnd[1].'/'.$rnd[2].'&start_time='.time().'&total_upload_size='.filesize($lfile).'&rnd_id='.$rndid.'';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $Url);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $lfile);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0) Gecko/20100101 Firefox/4.0');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*\/*;q=0.8', 'Accept-Language: de-de,de;q=0.8,en-us;q=0.5,en;q=0.3', 'Accept-Encoding: gzip, deflate', 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7', 'Connection: keep-alive', 'Content-Type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_REFERER, 'http://www.hellshare.com/');
			curl_setopt($ch, CURLOPT_COOKIE, $cookies);
			$data = curl_exec($ch);
			curl_close($ch);
			*/
			
			$url=parse_url($action_path);
			$fpost["this_file_num"] = "0";
			$fpost["embedded_upload_results"] = "1";
			$fpost["upload_file_folder_0"] = "0";
			$fpost["updealer_id_0"] = "";
			$fpost["rau"] = "www.hellshare.com";
			$fpost["description_0"] = "";
			$upfiles=upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://www.hellshare.com/", 0, $fpost, $lfile, $lname, "upfile_0");
			preg_match('#http:\/\/www\.hellshare\.com\/uu_finished_pro\.php\?rnd_id=([A-Za-z0-9]+)\&tmp_sid=([0-9]+)\/([a-z0-9]+)#', $upfiles, $infolink);
			$Url=parse_url('http://www.hellshare.com/hs_upload_process_pro.php?tmp_sid='.$infolink[2].'/'.$infolink[3].'');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.hellshare.com/", $cookies, 0, 0, $_GET["proxy"], $pauth);
			preg_match('#target="_top">(http:\/\/download\.hellshare\.com\/.+)<\/a>#', $page, $dllink);
			
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			$download_link=$dllink[1];
}       
?>