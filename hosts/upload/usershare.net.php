<?php 
// addon debug 

// end addon
####### Free Account Info. ###########
$usershare_username=""; //  Set you username
$usershare_password=""; //  Set your password
##############################

$not_done=true;
$continue_up=false;
if ($usershare_username & $usershare_password){
	$_REQUEST['my_login'] = $usershare_username;
	$_REQUEST['my_pass'] = $usershare_password;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["usershare.net"]; ?></b></small></tr>
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
            $referrer="http://usershare.net/"; 
            $Url = parse_url($referrer."login.html");
			$post['op'] = "login";
			$post['redirect'] = "";
			$post['ssl'] = "";
			$post['login'] = $usr;
			$post['password'] = $pass;
			$post['x'] = rand(1,50);
            $post['y'] = rand(1,20);
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$cookie =GetCookies($page);
            
			$Url = parse_url("http://usershare.net/");
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
            is_notpresent($page,"Logout","Not logged in. Check your login details in usershare.net.php");
			
            $tid3=rndNum(12);
			$formact=cut_str($page, '<div id="div_file">', '</div>');
			$url_action=cut_str($formact, 'action="', '"').$tid3."&js_on=1&utype=reg&upload_type=file";
			$fpost['upload_type'] = "file";
			$fpost['sess_id'] = cut_str($page, 'sess_id" value="', '"');
			$fpost['authu'] = cut_str($page, 'authu" value="', '"');
			$fpost['x'] = rand(1,50);
            $fpost['y'] = rand(1,20);
			$fpost['tos'] = "1";
			
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
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$referrer, $cookie, $fpost, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	

			$act=cut_str($upfiles,"action='","'");
			$dat=cut_str($upfiles,"POST'><textarea name=","/textarea></Form>");
			preg_match_all('/\'.+?</',$dat,$datas);
			$post=array();
			foreach ($datas[0] as $data){
			$tmp=str_replace(">","=",$data);
			$quer=str_replace("<","",$tmp);
            $tquer=explode("'",$quer);
			$post[$tquer[1]] = str_replace("=","",$tquer[2]);
			}	
			$Url=parse_url($act);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
			is_notpresent($page,"Files Uploaded","Error upload file",0);
			
			preg_match('/[^\'"]+-del-[^\'"]+/', $page, $del);

			$download_link=cut_str($page,"[URL=","]");
			$delete_link=$del[0];
			}
	
			function rndNum($lg){
            $str="0123456789"; 
			for ($i=1;$i<=$lg;$i++){
			$st=rand(0,9);
			$pnt.=substr($str,$st,1);
			}
            return $pnt;}
/*************************\  
written by kaox 13/06/2009
\*************************/
?>