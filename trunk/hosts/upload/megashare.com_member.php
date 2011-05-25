<?php

####### Account Info. ###########
$megashare_login = ""; //Set your mail login
$megashare_pass = ""; //Set your  password
##############################

$not_done=true;
$continue_up=false;
if ($megashare_login && $megashare_pass){
	$_REQUEST['my_login'] = $megashare_login;
	$_REQUEST['my_pass'] = $megashare_pass;
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
<tr><td nowrap>&nbsp;Email*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["megashare.com"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Megashare.com</div>
<?php 
                        
                        $post['loginid'] = $_REQUEST['my_login'];
			$post['passwd'] = $_REQUEST['my_pass'];
                        $post['yes'] = "submit";
			$page = geturl("upload.megashare.com", 80, "/login.php", "http://upload.megashare.com/login.php", 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
                        is_present($page, 'Invalid Username or Password.', 'Invalid Username or Password !.');
                        $cookie = GetCookies($page);
                        $page = geturl("upload.megashare.com", 80, "/", "http://upload.megashare.com/login.php", $cookie, 0, 0, $_GET["proxy"], $pauth);
                        is_page($page);	
                        
	?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<script>document.getElementById('info').style.display='none';</script>
<?php 
                        $url_up = cut_str($page, '<form name="form_upload" method="post" enctype="multipart/form-data"  action="','" ');
                        $fpost = array(
			'emai' => $_REQUEST['my_login'],
			'upload_range' => '1');
                        $upurl= "http://upload.megashare.com".$url_up;
			$url = parse_url($upurl);
		        $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://upload.megashare.com/", $cookie, $fpost, $lfile, $lname, "upfile_0");
                        
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
                        $dwn=trim(cut_str($upfiles,"Location: ","&close_pop=1"));
                        $Url=parse_url($dwn);
                        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://bitshare.com/", $cookie, 0, 0, $_GET["proxy"],$pauth);  		
			is_notpresent($page,'Download Link','Error Get Download Link!!!');
                        $download_link = cut_str($page, '<INPUT TYPE="text" name="dlink" class="inputbox" maxlength="51" SIZE="50" readonly="0" VALUE="','">');
                        $delete_link = cut_str($page, '<INPUT TYPE="text" name="rlink" class="inputbox" maxlength="100" SIZE="100" readonly="0" VALUE="','">');	
	}
// written by VinhNhaTrang 12/01/2011
?>