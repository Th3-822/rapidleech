<?php

// ===== Account Information ==============
$downupload_login = ""; // Set your Downupload Username
$downupload_pass = ""; //Set your Downupload Password
// =======================================

$not_done=true;
$continue_up=false;
if ($downupload_login & $downupload_pass){
    $_REQUEST['login'] = $downupload_login;
    $_REQUEST['password'] = $downupload_pass;
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
			<tr><td nowrap>&nbsp;Login*<td>&nbsp;<input type=text name=login value='' style="width:160px;" />&nbsp;</tr>
			<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=password value='' style="width:160px;" />&nbsp;</tr>
			<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
			<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["downupload.com_member"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to downupload.com</div>
<?php
			$Url=parse_url("http://www.downupload.com/");
            $post['op'] = "login" ;
            $post['redirect'] = "" ;
            $post['login'] = $_REQUEST['login'];
            $post['password'] = $_REQUEST['password'];
            $post['x'] = "0" ;
            $post['y'] = "0" ;
            $page = geturl($Url["host"], 80, "/login.html", 0, 0, $post, 0, $_GET["proxy"], $pauth);
            is_page($page);
            is_notpresent($page, 'HTTP/1.1 302 Moved', 'Error logging in - Are your logins correct?');
            preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
            $cookie = $temp[1];
            $cookies = implode(';',$cookie);
            $xfss=cut_str($cookies,'xfss=',' ');
            $page = geturl($Url["host"], 80, "/?op=my_files", "http://www.downupload.com/login.html", $cookies, 0, 0, "");
            is_page($page);
            is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - Are your logins correct?');
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
    $ref='http://www.downupload.com/';
    $Url=parse_url($ref);
    $page = geturl($Url["host"], 80, "/?op=upload", 0, 0, 0, 0, $_GET["proxy"],$pauth);
    is_page($page);
    $upfrm = cut_str($page,'multipart/form-data" action="','cgi-bin/upload.cgi?');
    $uid = $i=0; while($i<12){ $i++;}
    $uid += floor(rand() * 10);
    $post['upload_type']= 'file';
    $post['sess_id']= $xfss;
    $post['file_0_descr']=$_REQUEST['descript'];
    $post['file_0_public']='1';
    $post['link_rcpt']='';
    $post['link_pass']='';
    $post['tos']='1';
    $post['submit_btn']=' Upload! ';
    $uurl= 'http://www.downupload.com/cgi-bin/upload.cgi?upload_id='.$uid.'&js_on=1&utype=prem&upload_type=file';
    $url=parse_url('http://www.downupload.com/cgi-bin/upload.cgi?upload_id='.$uid.'&js_on=1&utype=prem&upload_type=file');
?>
<script>document.getElementById('info').style.display='none';</script>
<?

    $upfiles=upfile($Url["host"],80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
    $locat=cut_str($upfiles,'rea name=\'fn\'>' ,'</textarea>');
    unset($post);
    $gpost['fn'] = "$locat" ;
    $gpost['st'] = "OK" ;
    $gpost['op'] = "upload_result" ;
    $Url=parse_url($ref);
    $page = geturl($Url["host"], 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $uurl, $cookies, $gpost, 0, $_GET["proxy"],$pauth);
    $ddl=cut_str($page,'style="width:98%;" rows=3 onFocus="copy(this);">','<');
    $download_link = $ddl;
}

/****************************\
Written by Darkra 30/04/2011
\****************************/

?>