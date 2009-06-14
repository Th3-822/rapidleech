<?php 

####### Free Account Info. ##################
$kewlshare_username=""; //  Set you username
$kewlshare_password=""; //  Set your password
#############################################

$not_done=true;
$continue_up=false;
if ($kewlshare_username & $kewlshare_password){
	$_REQUEST['my_login'] = $kewlshare_username;
	$_REQUEST['my_pass'] = $kewlshare_password;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["kewlshare.com"]; ?></b></small></tr>
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
            $referrer="http://kewlshare.com/index.php"; 
            $Url = parse_url("http://kewlshare.com/login.php");
			$post['login'] = "null";
			$post['username'] = $usr;
			$post['password'] = $pass;
			$post['x'] = rand(1,50);
            $post['y'] = rand(1,20);
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$cookie =grabbCookie($page);
			$Url = parse_url("http://kewlshare.com/upload.php");
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
            is_notpresent($page,"Logout","Not logged in. Check your login details in kewlshare.com.php");	
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
			$referrer="http://kewlshare.com/upload.php";
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
			preg_match('/http:\/\/kewlshare\.com\/dl\/[^\'"]+/i', $page, $down);
			preg_match('/http:\/\/kewlshare\.com\/delete\/[^\'"]+/i', $page, $del);

			$download_link=$down[0];
			$delete_link=$del[0];
			}
			function grabbCookie($content)
			{
				preg_match_all('/Set-Cookie: (.*);/U',$content,$temp);
				foreach ($temp[1] as $tmp){
				if(strpos($tmp,"=deleted")===false ){
				$cook .= $tmp."; ";}}
				$cook=substr($cook, 0, -2); 
				return  $cook;
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