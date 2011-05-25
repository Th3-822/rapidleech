<?php

####### Account Info. ###########
$uploading_com_login = "xxxxxx"; //Set you username : email
$uploading_com_pass = "xxxxxx"; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($uploading_com_login & $uploading_com_pass){
	$_REQUEST['my_login'] = $uploading_com_login;
	$_REQUEST['my_pass'] = $uploading_com_pass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["uploading.com_premium"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Uploading.com</div>
<?php 
                        $post=array();
	                $post['email'] = $_REQUEST['my_login'];
                        $post['password'] = $_REQUEST['my_pass'];
                        $post["remember"]="on";
                        $page = geturl("uploading.com", 80, "/general/login_form/", 0, 0, $post, 0 );
                        $cookie=GetCookies($page);
                        $page = geturl("uploading.com", 80, "/", "http://uploading.com/login_form/", $cookie);
                        is_page($page);
                        is_notpresent($page, "top_user_panel", "Login failed<br>Wrong login/password?");
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			preg_match("/upload_url: '(.*)'/", $page, $upurl);
                        $upurl = trim($upurl[1]);
                        preg_match("/SID: '(.*)'/", $page, $SID);
                        $SID = trim($SID[1]);
                        $url=parse_url($upurl);
?>
<?php 	

                        $post = array();
			$post["Filename"] = $lname;
			$post["SID"] = $SID;
			$post["label_id"] = '0';
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://www.uploading.com/",$cookie, $post, $lfile, $lname, "file");
			
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
                        $path_done =cut_str($upfiles,'answer":"','"');
                        $done_url = "http://uploading.com/files/done/$path_done";
                        $Url=parse_url($done_url);
                        $page = geturl($Url["host"], $url["port"] ? $url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://uploading.com/files/upload/", $cookie, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
			preg_match('/http:\/\/uploading\.com\/files\/\w{7,9}\/[^\'"]+/i', $page, $preg) or html_error("Upload error");
                        preg_match('/http:\/\/uploading\.com\/files\/edit\/[^\'"]+/i', $page, $pregd);
                        $download_link = $preg[0];
                        $delete_link = $pregd[0];
                        echo "<h3><font color='green'>File successfully uploaded to your account</font></h3>";  
	}
/*************************\
WRITTEN by VinhNhaTrang 29/10/2010
\*************************/
?>