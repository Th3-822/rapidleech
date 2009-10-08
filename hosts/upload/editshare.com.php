<?php
####################################################################################################################
######################### MODULE UPLOAD PLUGIN FOR RAPIDLEECH - WRITTEN BY KAOX ####################################
####################################################################################################################

$site_login = '';            // site_login
$site_pass = '';             // site_pass
$mode = "guest";            // switch this variable if must upload in "guest" "member" or "premium"

$form =<<<FORM
     
<table border=0 style="width:350px;" cellspacing=0 align=center> <form method=post> <input type=hidden name=action value='FORM' /> <tr><td nowrap>&nbsp;Username*</td><td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</td></tr> <tr><td nowrap>&nbsp;Password*</td><td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</td></tr> <tr><td colspan=2 align=center><input type=submit value='Upload'></td></tr> </form> </table>       
FORM;

$not_done= true;

	if ($mode!="guest" && $mode!="member" && $mode!="premium")
	{
	html_error("mistake in \$mode - Please riedit the plugin not let \$mode variable blank or different from guest,member or premium value" , 0 ); 
	}
	if ($mode == "member" or $mode == "premium")
	{
		if ($_REQUEST['action'] == "FORM")
		{
		$continue_up = true;
		$user= $_REQUEST['my_login'];
		$pass= $_REQUEST['my_pass'];
		}elseif($site_login && $site_pass)
		{
		$continue_up = true;
		$user = $site_login;
		$pass = $site_pass;
		echo "<b><center>Use Default login/pass.</center></b>";
		}else{ 
		     echo $form;
		     }
	}
?>
<table width=600 align=center> </td></tr> <tr><td align=center> <script>document.getElementById('login').style.display='none';</script> <script>document.getElementById('info').style.display='none';</script> <script>document.getElementById('progressblock').style.display='none';</script> 
<?php

switch ($mode){
  
	case "guest":
	echo ('<b><center style="color: #C0C0C0"><span style="background-color: #F8F8B6">Use guest mode</span></center></b><br>');
///////////////////////////////////////////////////// START ////////////////////////////////////////////////////////

$Url=parse_url("http://www.editandshare.com/");
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
$cookie = GetCookies($page);
$url=parse_url("http://www.editandshare.com/upload");
$APC_UPLOAD_PROGRESS = cut_str($page,'progress_key" value="','"');
$post=array();
$post['APC_UPLOAD_PROGRESS']=$APC_UPLOAD_PROGRESS;
$page = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), $referrer , $cookie, $post, $lfile, $lname, "file1");
$snap1 = cut_str ( $page ,'Download link:' ,'Delete link:' );
$snap2 = cut_str ( $page ,'Delete link:' ,'</div>' );
$download_link = cut_str ( $snap1 ,'href="' ,'"' );
$delete_link = cut_str ( $snap2 ,'href="' ,'"' );

///////////////////////////////////////////////////// END //////////////////////////////////////////////////////////
	$not_done= false;
	break;

	case "member":
	if ($continue_up){
	echo ('<b><center style="color: #00FF00"><span style="background-color: #F8F8B6">Use member mode</span></center></b><br>');
///////////////////////////////////////////////////// START ////////////////////////////////////////////////////////

///////////////////////////////////////////////////// END //////////////////////////////////////////////////////////
	$not_done= false;
	break; 
}

	case "premium":
	if ($continue_up){
	echo ('<b><center style="color: #FF0000"><span style="background-color: #F8F8B6">Use premium mode</span></center></b><br>');
///////////////////////////////////////////////////// START ////////////////////////////////////////////////////////

///////////////////////////////////////////////////// END //////////////////////////////////////////////////////////
	$not_done= false;
	break; 

}
}
/*************************\
 WRITTEN BY KAOX 06-oct-09
\*************************/
?>