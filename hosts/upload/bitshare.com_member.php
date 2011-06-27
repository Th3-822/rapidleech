<?php

####### Free Account Info. ###########
$bitshare_login = "xxxxxx"; //Set your id (login)
$bitshare_pass = "xxxxxx"; //Set your  password
##############################

$not_done=true;
$continue_up=false;
if ($bitshare_login && $bitshare_pass){
	$_REQUEST['my_login'] = $bitshare_login;
	$_REQUEST['my_pass'] = $bitshare_pass;
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
<tr><td nowrap>&nbsp;User*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["bitshare.com"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to Bitshare.com</div>
<?php 
                        $post['user'] = $_REQUEST['my_login'];
			$post['password'] = $_REQUEST['my_pass'];
                        $post['submit'] = "Login";
			$page = geturl("bitshare.com", 80, "/login.html", "http://bitshare.com/", 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
                        is_notpresent($page, 'HTTP/1.1 302', 'Error logging in - are your logins correct!');
                        $cookie = GetCookies($page);
                        $page = geturl("bitshare.com", 80, "/", "http://bitshare.com/", $cookie, 0, 0, $_GET["proxy"], $pauth);
                        is_page($page);		
	?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<script>document.getElementById('info').style.display='none';</script>
<?php 
                        preg_match('/<form action="(.*)" id="uploadform" method="post"/', $page, $upurl);
                        $url_up = trim($upurl[1]);
                        $progress_key = cut_str($page, '<input type="hidden" name="APC_UPLOAD_PROGRESS" id="progress_key"  value="','"/>');
                        $usergroup_key = cut_str($page, '<input type="hidden" name="APC_UPLOAD_USERGROUP" id="usergroup_key"  value="','"/>');
                        $fpost = array(
			'APC_UPLOAD_PROGRESS' => $progress_key,
			'APC_UPLOAD_USERGROUP' => $usergroup_key,
			'UPLOAD_IDENTIFIER' => $progress_key,
                        'file[]"; filename="' => '');
                        $ID = GRC();
                        $upurl=$url_up.'?X-Progress-ID=undefined'.$ID.'';
			$url=parse_url($upurl);
		        $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://bitshare.com/", $cookie, $fpost, $lfile, $lname, "file[]");                        
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
                        $dwn=trim(cut_str($upfiles,"Location: ","\n"));
                        $Url=parse_url($dwn);
                        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://bitshare.com/", $cookie, 0, 0, $_GET["proxy"],$pauth);  		
			is_notpresent($page,'Download Link','Error Get Download Link!!!');
                        preg_match('%http://bitshare.com/files/(.*)%', $page, $dlink);
                        preg_match('%http://bitshare.com/delete/(.*)%', $page, $delink);
                        $download_link=$dlink[0];
                        $delete_link= $delink[0];		
	}
function GRC($length=32,$letters='abcdefklmnoupqrstvx1234567890')
  {
      $s = '';
      $lettersLength = strlen($letters)-1;
      for($i = 0 ; $i < $length ; $i++)
      {
      $s .= $letters[rand(0,$lettersLength)];
      }
      return $s;
  } 
// written by VinhNhaTrang 04/11/2010
?>
