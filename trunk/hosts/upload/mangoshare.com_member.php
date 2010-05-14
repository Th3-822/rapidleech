<?php 

####### Free Account Info. ###########
$mango_username=""; //  Set you username
$mango_password=""; //  Set your password
##############################

$not_done=true;
$continue_up=false;
if ($mango_username & $mango_password){
	$_REQUEST['my_login'] = $mango_username;
	$_REQUEST['my_pass'] = $mango_password;
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
<tr><td colspan=2 align=center><small>*You can set it as default in mangoshare.com.php</small></tr>
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
<?php
            $usr=$_REQUEST['my_login'];
            $pass=$_REQUEST['my_pass'];
			$ref="http://mangoshare.com/login.html";
			$in=parse_url("http://mangoshare.com/");
			$post=array();
			$post["redirect"]="http://www.mangoshare.com/";
			$post["login"]=$usr;
			$post["password"]=$pass;
			$post["op"]="login";
			$page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), $ref, 0, $post, 0, $_GET["proxy"],$pauth);	
			$cook = GetCookies($page);
			is_notpresent($cook, "xfss=", "Incorrect Login or Password")
	?>
<script>document.getElementById('info').style.display='none';</script>

		<table width=600 align=center>
			</td>
			</tr>
			<tr>
				<td align=center>
<?php		
		  $in=parse_url("http://www.mangoshare.com/");
	      $page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), $ref, $cook, 0, 0, $_GET["proxy"],$pauth);	

		  preg_match_all('/(action)[ ="]+.+?"/', $page, $act);
		  $action = preg_replace('/(action)[ ="]+/i', '', $act[0][0]);
		  $action = str_replace("\"","",$action); 
		  $uid = $i=0; while($i<12){ $i++;}
		  $uid += floor(rand() * 10);
		  $action.= "?upload_id=$uid&js_on=1&utype=reg&upload_type=file";
		  $url=parse_url($action);
		  $fpost=array();
		  $fpost["upload_type"]="file";
		  $fpost["link_rcpt"]="";
		  $fpost["link_pass"]="";
		  $fpost["tos"]="1";
		  $fpost["submit_btn"]=" Upload!";
		  
		  $sess_id = trim ( cut_str ( $page, '<input type="hidden" name="sess_id" value="', '"' ) );
		  $srv_tmp_url = trim ( cut_str ( $page, '<input type="hidden" name="srv_tmp_url" value="', '"' ) );		
		  $fpost["sess_id"]=$sess_id;
		  $fpost["srv_tmp_url"]=$srv_tmp_url;
		  $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $ref, $cook, $fpost, $lfile, $lname, "file_1");
		  
	$locat=cut_str($upfiles,'<textarea name=\'fn\'>' ,'</textarea>');
	unset($post);
	$gpost['fn'] = "$locat" ;
	$gpost['st'] = "OK" ;
	$gpost['op'] = "upload_result" ;
	$Url=parse_url("http://mangoshare.com/");
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $action, $cook, $gpost, 0, $_GET["proxy"],$pauth);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	

		  preg_match('/http:\/\/.+killcode[^\'"]+/', $page, $del);
		  $download_link = trim ( cut_str ( $page, 'onFocus="copy(this);">[URL=', ']' ) );
		  if(empty($download_link)){
		  write_file($options['download_dir']."mango_err.log", $page);
		  html_error("Error, no download link retrieved, check error log.","0");
		  }
		  $delete_link = $del['0'];
		}
			
		// written by billybob187 2010-05-14
?>