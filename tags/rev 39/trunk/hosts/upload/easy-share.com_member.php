<?php
$easy_login = ""; // login
$easy_pass = ""; // password
$not_done = true;
$continue_up = false;
$cook = "";
if ($easy_login & $easy_pass) {
	$_REQUEST ['my_login'] = $easy_login;
	$_REQUEST ['my_pass'] = $easy_pass;
	$_REQUEST ['action'] = "FORM";
	echo "<b><center>Use Default easy-share.com login/pass.</center></b>\n";
}

if ($_REQUEST ['action'] == "FORM")
	$continue_up = true; else {
	?>
<table border=1 style="width: 540px;" cellspacing=0 align=center>
	<form method=post><input type=hidden name=action value='FORM' />
	
	<tr >
	  <td colspan=4 align=center height=25px ><b>Enter Premium Account</b> </td>
	</tr>
	<tr>
		<td nowrap>&nbsp;Login		
		<td>&nbsp;<input name=my_login value='' style="width: 160px;" />&nbsp;		
		<td nowrap>&nbsp;Password		
		<td>&nbsp;<input type=password name=my_pass value='' style="width: 160px;" />&nbsp;	
	</tr>	
	<tr><td colspan=4 align=center><input type=submit value='Upload' /></tr>	
</table>
</form>
<?php
}

if ($continue_up) {
	$lang = ";language=en";  
	$not_done = false;
	if (empty ( $_REQUEST ['my_login'] ) || empty ( $_REQUEST ['my_pass'] )) {
		echo "<b><center>Empty login/pass easy-share.com.</center></b>\n";
	} else {
		?>
<div id=login width=100% align=center>Login to easy-share.com</div>
<?php
		$Url = parse_url("http://www.easy-share.com/accounts/login");
		$post = array();
  $post["login"] = $_REQUEST ['my_login'] ;
  $post["password"] = $_REQUEST ['my_pass'] ;
  $post["remember"] = "1";
  $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/accounts/login", "http://easy-share.com", 0, $post, 0, $_GET["proxy"],$pauth);
		is_page ( $page );
		if (!strpos ( $page, "accounts" )) {
		html_error("error login, check your login/pass") ;
        } else {
		preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cook = implode(';',$cookie).$lang;
            $sid  =  cut_str($cook,'PHPSESSID=',';');
		}
	}
	?>
<script>document.getElementById('login').style.display='none';</script>


<table width=600 align=center>
	</td>
	</tr>
	<tr>
		<td align=center>

		<div id=info width=100% align=center>Retrieve upload ID</div>
<?php
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/", 0, $cook, 0, 0, $_GET["proxy"],$pauth);
	?>
	<script>document.getElementById('info').style.innerHTML='Connected to netload, retrieving form...';</script>
<?php
	is_page ( $page );
	unset ( $post );
	$url_action = "http://upload.easy-share.com/accounts/upload_backend/perform/ajax";
	$post['Filename'] = $lname;
	$post['language'] = 'en';
	$post['user'] = cut_str($page,'user": "','"');
    $post['PHPSESSID'] =$sid;
	$url = parse_url ( $url_action );
	}
?>
	<script>document.getElementById('info').style.innerHTML='Uploading...';</script>
<?php
if ($not_done==true){die;}
	$upfiles = upfile ( $url ["host"], $url ["port"] ? $url ["port"] : 80, $url ["path"] . ($url ["query"] ? "?" . $url ["query"] : ""), "http://easy-share.com/index.php?id=1", 0, $post, $lfile, $lname, "Filedata" );
	
	?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	is_page ( $upfiles );
                
    preg_match_all('/http:\/\/[^\'"<]+/',$upfiles,$temp);             
	$download_link =  $temp[0][0]; 
	$delete_link = $temp[0][1]; 
	
/*
easy-share member upload plug-in writted by kaox 25/04/09
*/
?>