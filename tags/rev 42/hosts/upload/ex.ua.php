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
$Url=parse_url("http://www.ex.ua/edit_storage");
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://www.ex.ua/storage", 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
$locat=cut_str ($page ,"Location: ","\r"); 
$Url=parse_url($locat);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://www.ex.ua/storage", 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

$key = cut_str ( $page ,'key		: "' ,'"' );
$object_id = cut_str ( $page ,'object_id	: ' ,',' );
$action_id = cut_str ( $page ,"action_id	: ","\r" );
$time=str_replace('.','',microtime(1)).'0';

$post=array();
$post['Filename']=$lname;
$post['key']=$key;
$post['object_id']=$object_id;
$post['action_id']=$action_id;
$post['time']=$time;
$post['Upload']='Submit Query';

$url=parse_url("http://fs19.www.ex.ua/r_upload");

$page = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), $referrer , $cookie, $post, $lfile, $lname, "Filedata");
is_page($page);

	echo
<<<HTML
<table border=0 style="width:270px;" cellspacing=0 align=center>
<tr><td nowrap>&nbsp;File succesfull uploaded. Go in your tempdirectory<td></tr>
<tr><td align="center"><a href="http://www.ex.ua/view_storage/$key" target="_blank"> -= Click =-</a><td></tr>
</table>
HTML;

 
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

?>