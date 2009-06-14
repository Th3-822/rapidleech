<?php 

####### Free Account Info. ###########
$uploaded_username=""; //  Set you username
$uploaded_password=""; //  Set your password
##############################

$not_done=true;
$continue_up=false;
if ($uploaded_username & $uploaded_password){
	$_REQUEST['my_login'] = $uploaded_username;
	$_REQUEST['my_pass'] = $uploaded_password;
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
<tr><td nowrap>&nbsp;Username*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["uploaded.to"]; ?></b></small></tr>
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
<div id=info width=100% align=center>Retrive upload ID</div>
<?			
            $usr=$_REQUEST['my_login'];
            $pass=$_REQUEST['my_pass'];
            $referrer="http://uploaded.to/login"; 
            $Url = parse_url($referrer);
			$post['email'] = $usr;
			$post['password'] = $pass;
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$cookie =GetCookies($page);
            
			$Url = parse_url("http://uploaded.to/home");
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
            is_notpresent($page,"Logout","Not logged in. Check your login details in uploaded.to.php");
			
            $tid3=rndNum(9);
			$formact=cut_str($page, 'document.F1.action = "', '"');
			preg_match('/http:\/\/upload[^\/]+/', $formact, $upf);
			$url_action=$upf[0]."/up?upload_id=".$tid3;
			$fpost['x'] = rand(1,50);
            $fpost['y'] = rand(1,20);
			
	?>
<script>document.getElementById('info').style.display='none';</script>

		<table width=600 align=center>
			</td>
			</tr>
			<tr>
				<td align=center>
<?php		
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$referrer, $cookie, $fpost, $lfile, $lname, "file1x");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
	
			$Url=parse_url("http://uploaded.to/");
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
		    $inputdwn=cut_str($page, 'name="dllinks" value="', '/>');
			$inputadm=cut_str($page, 'name="adminlinks" value="', '/>');
			preg_match('/\w{6}/', $inputdwn, $dwn);
			preg_match('/\w{30,}/', $inputadm, $adm);
			$download_link="http://ul.to/".$dwn[0];
			$adm_link = "http://ul.to/".$adm[0];
			}
			function rndNum($lg){
            $str="0123456789"; 
			for ($i=1;$i<=$lg;$i++){
			$st=rand(0,9);
			$pnt.=substr($str,$st,1);
			}
            return $pnt;}
/*************************\  
written by kaox 14/06/2009
\*************************/
?>