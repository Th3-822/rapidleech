<?php

####### Free Account Info. ###########
$shareator_login = ""; //Set you username
$shareator_pass = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($shareator_login & $shareator_pass){
	$_REQUEST['my_login'] = $shareator_login;
	$_REQUEST['my_pass'] = $shareator_pass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["free-uploading.com"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to turboupload.com</div>
<?php      
 $ref="http://www.turboupload.com/";
 $post['login'] = $_REQUEST['my_login'];
 $post['password'] = $_REQUEST['my_pass'];
 $post['op'] = 'login';
 $post['x'] = 34;
 $post['y'] = 10;

$page = geturl("www.turboupload.com", 80, "/", $ref, 0, $post, 0, "");

			$cookie1 = "login=".cut_str($page,'Cookie: login=',";")."; ";
			$cookie1 .= "domain=.turboupload.com"."; ";
			$cookie1 .= "path=/"."; ";
			$cookie1 .= "xfss=".cut_str($page,'Cookie: xfss=',";")."; ";

$page = geturl("www.turboupload.com", 80, "/", $ref, $cookie1, 0, 0, "");

                  $tmp=cut_str($page, 'multipart/form-data" action="','"');
                  $action_url = $tmp.'/?X-Progress-ID='.rand(100000000000, 999999999999);
			$url = parse_url($action_url);
			$post['upload_type'] = 'file';
			$post['submit_btn'] = ' Upload! ';
			$tmp2= cut_str($page, 'name="sess_id" value="','"');
                  $post['sess_id']=$tmp2;
?>
<script>document.getElementById('info').style.display='none';</script>
<?php		
            $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://turboupload.com/', $cookie1, $post, $lfile, $lname, "file_0");
			is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php	
			$tempsix=cut_str($upfiles, 'Location: http://www.turboupload.com/','_result');
                  $pepurl=$tempsix."_result";
$page = geturl("www.turboupload.com", 80, "/".$pepurl, $ref, $cookie1, 0, 0, "");

$download_link="http://www.turboupload.com/".cut_str($page, 'copy(this);" value="http://www.turboupload.com/','"');
 }
?>
