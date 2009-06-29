<?php 

####### Free Account Info. ###########
$hotfile_username=""; //  Set you username
$hotfile_password=""; //  Set your password
##############################

$not_done=true;
$continue_up=false;
if ($hotfile_username & $hotfile_password){
	$_REQUEST['my_login'] = $hotfile_username;
	$_REQUEST['my_pass'] = $hotfile_password;
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
<tr><td colspan=2 align=center><small>*You can set it as default in hotfile.com.php</small></tr>
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
			$ref="http://hotfile.com/";
			$in=parse_url("http://hotfile.com/login.php");
			$post=array();
			$post["returnto"]="/";
			$post["user"]=$usr;
			$post["pass"]=$pass;
			$page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), $ref, 0, $post, 0, $_GET["proxy"],$pauth);	
			preg_match('/auth=\w{64}/i', $page, $ook);
			$cook=$ook[0];
			if(!$cook){
			html_error("Login Failed , Bad username/password combination.",0);
			}
			
	?>
<script>document.getElementById('info').style.display='none';</script>

		<table width=600 align=center>
			</td>
			</tr>
			<tr>
				<td align=center>
<?php		
		  $in=parse_url("http://hotfile.com/");
	      $page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), $ref, $cook, 0, 0, $_GET["proxy"],$pauth);	

		  preg_match_all('/(action)[ ="]+.+?"/', $page, $act);
		  $action = preg_replace('/(action)[ ="]+/i', '', $act[0][0]);
		  $action = str_replace("\"","",$action); 
		  $url=parse_url($action);
		  $fpost=array();
		  $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $ref, $cook, $fpost, $lfile, $lname, "uploads[]");
		  
		  if(preg_match('/Location: *(.+)/', $upfiles, $redir)){
		  $redirect=rtrim($redir["1"]);
		  $Url = parse_url($redirect); 
		  }else{html_error("Error, no download link retrieved","0");}        
		  $page=geturl($Url['host'],"80",$Url ["path"]);
 

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	

		  preg_match('/http:\/\/.+dl[^\'"]+/', $page, $dwn);  
		  preg_match('/http:\/\/.+kill[^\'"]+/', $page, $del);
		  $download_link = $dwn['0'];
		  $delete_link = $del['0'];
		}
			
		// written by kaox 09/06/2009
?>