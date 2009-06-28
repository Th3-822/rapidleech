<?php 

####### Free Account Info. ###########
$duckload_username=""; //  Set you username
$duckload_password=""; //  Set your password
##############################

$not_done=true;
$continue_up=false;
if ($duckload_username & $duckload_password){
	$_REQUEST['my_login'] = $duckload_username;
	$_REQUEST['my_pass'] = $duckload_password;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["duckload.com"]; ?></b></small></tr>
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
            $referrer="http://duckload.com/account.html"; 
            $Url = parse_url("http://duckload.com/index.php?Modul=Login");
			$post['yl_name'] = $usr;
			$post['yl_pw'] = $pass;
			$post['yl_submit'] = "Login";

            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page); 
    preg_match('/Set-Cookie: (.*);/U',$page,$tmp);
    $cook .= $tmp[1];		
            
            $referrer="http://duckload.com/index.html";
              
			$Url = parse_url("http://duckload.com/member/&auth=true");
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cook, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
            is_notpresent($page,$usr,"Not logged in. Check your login details in duckload.com.php");            
            $cookie =GetCookies($page);
	        $cookie =$cook ."; ".GetCookies($page);
            $Url = parse_url($referrer);
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
            is_page($page) ;
            for($i=0; $i<32; $i++){
            $id .= base_convert(floor(rand(0, 15)), 10, 16);}
			$url_action=cut_str($page, 'action="', '"')."?X-Progress-ID=".$id;
			$fpost['MAX_FILE_SIZE'] = "9589934592";

			
	?>
<script>document.getElementById('info').style.display='none';</script>

		<table width=600 align=center>
			</td>
			</tr>
			<tr>
				<td align=center>
<?php		
			
			$url = parse_url($url_action);
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$referrer, 0, $fpost, $lfile, $lname, "file[]");

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
			is_notpresent($page,"Upload Erfolgreich","Error upload file",0);
			
			preg_match('/http:\/\/duckload\.com\/download\/[^"\']+/i', $page, $down);
			preg_match('/http:\/\/duckload\.com\/divx\/[^"\']+/i', $page, $divx);
			preg_match('/http:\/\/duckload\.com\/delete\/[^"\']+/i', $page, $del);
			
			
			$download_link=$down[0];
			$stat_link=$divx[0];
			$delete_link=$del[0];
			}			


/*************************\  
written by kaox 28/06/2009
\*************************/
?>