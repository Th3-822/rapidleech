<?php
####### Free Account Info. ###########
$so_login = "jijack021@hotmail.fr";
$so_pass = "badreddine";
##############################

$not_done=true;
$continue_up=false;
if ($so_login & $so_pass){
	$_REQUEST['bin_login'] = $so_login;
	$_REQUEST['bin_pass'] = $so_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Let it empty for free user</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=1 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM'><input type=hidden value=uploaded value'<?php $_REQUEST[uploaded]?>'>
<input type=hidden name=filename value='<?php echo base64_encode($_REQUEST[filename]); ?>'>
<tr><td nowrap>&nbsp;Login<td>&nbsp;<input name=bin_login value='' style="width:160px;">&nbsp;</tr>
<tr><td nowrap>&nbsp;Password<td>&nbsp;<input name=bin_pass value='' style="width:160px;">&nbsp;</tr>
<tr><td colspan=2 align=center>Let it empty for free user</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload'></tr>
</table>
</form>
<?
}

if ($continue_up)
	{
		$not_done=false;
?>
<table width=600 align=center> 
</td></tr> 
<tr><td align=center> 
<div id=login width=100% align=center>Login to site</div> 
<?php
			$ref='http://www.share-online.biz/';
			$Url=parse_url($ref);
			if ($_REQUEST['action'] == "FORM")
			{
				$post["act"]="login";
				$post["location"]="index.php";
				$post["dieseid"]="";
				$post["user"]=$_REQUEST['bin_login'];
				$post["pass"]=$_REQUEST['bin_pass'];
				$post["login"]="Log me in";
			
			
			$page = geturl($Url["host"],80,"/login.php",$ref,0,$post);
			$cookie1 = "PHPSESSID=".cut_str($page,'Cookie: PHPSESSID=',";")."; ";
			$cookie2 = "king_passhash=".cut_str($page,'Cookie: king_passhash=',";")."; ";
			$cookie3 = "king_sess_id=".cut_str($page,'Cookie: king_sess_id=',";")."; ";
			$cookie4 = "king_last_click=".cut_str($page,'Cookie: king_last_click=',";")."; ";
			$cookie5 = "king_uid=".cut_str(cut_str($page,'king_uid',true),'Cookie: king_uid=',';')."; ";
			$cookie6 = "king_logined=1"."; ";
			$cookie7 = "king_mylang=en"."; ";
			$cookies = "$cookie3$cookie6$cookie5$cookie4$cookie1$cookie2";
			
			$page = geturl($Url["host"],80,"/members.php", 'http://www.share-online.biz/index.php', $cookies,$post);
			}	
?>
<script>document.getElementById('login').style.display='none';</script> 
<div id=info width=100% align=center>Retrive upload ID</div> 
<?
			
            $page = geturl($Url["host"],80,"/",$ref,$cookies);
 
            $temp = cut_str($page,"name=uploadform",">"); 
            $action_url = cut_str($temp,'action="','"'); 
            $url = parse_url($action_url); 
            unset($post);
            $post["sessionid"]=cut_str($temp,"sid=","&"); 
            $post["UploadSession"] = $post["sessionid"]; 
            $post["AccessKey"] = cut_str($page,'AccessKey" value="','"'); 
            $post["maxfilesize"]= cut_str($temp,'maxfilesize" value="','"'); 
            $post["phpuploadscript"]=cut_str($page,'phpuploadscript" value="','"'); 
            $post["returnurl"]=cut_str($page,'returnurl" value="','"'); 
            $post["uploadmode"]="1"; 
             
?> 
<script>document.getElementById('info').style.display='none';</script> 
<?php
            $upfiles=upfile($url['host'],defport($url),$url['path']."?".$url["query"],$ref, $cookies, $post, $lfile, $lname, "uploadfile_0"); 

?> 
<script>document.getElementById('progressblock').style.display='none';</script> 
<div id=info2 width=100% align=center>Get links</div> 
<?php
            is_page($upfiles);

            $temp2 = trim(cut_str($upfiles,"Location: ","\n")); 
            $locat = parse_url($temp2); 
            sleep(5); 
            flush(); 
            $page2=geturl($locat["host"],80,$locat['path']."?".$locat["query"],$ref,0,0,0,0,0); 
            $path="/emaillinks.php?UploadSession=".$post["UploadSession"]."&AccessKey=".$post["AccessKey"]."&uploadmode=1&submitnums=0&fromemail=&toemail="; 
         
            $page3=geturl($Url["host"],80,$path,$ref,$cookies,0,0,0,0); 
            is_page($page3); 
            $download_link = trim(cut_str($page3,'downloadurl">','<')); 
            $delete_link = trim(cut_str($page3,'filedel">','<')); 
?> 
<script>document.getElementById('info2').style.display='none';</script>
<?
	}

// sert 25.08.2008
// Updated by Baking 27/06/2009 13:14
// Updeted Member user 30/06/2009 12:15
?>