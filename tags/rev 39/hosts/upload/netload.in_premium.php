<?php
##########################################
$mega_login = ""; // login
$mega_pass = ""; // password
##########################################
$not_done = true;
$continue_up = false;
$cook = "";
if ($mega_login & $mega_pass) {
	$_REQUEST ['my_login'] = $mega_login;
	$_REQUEST ['my_pass'] = $mega_pass;
	$_REQUEST ['action'] = "FORM";
	echo "<b><center>Use Default Netload.in login/pass.</center></b>\n";
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
	<tr>
		<td nowrap colspan=0>&nbsp;Description		
		<td colspan=3>&nbsp;<input name=message value='<?php print $mega_desc; ?>' style="width: 428px;" />&nbsp;			
	</tr>
	<tr><td colspan=4 align=center><input type=submit value='Upload' /></tr>	
</table>
</form>
<?php
}

if ($continue_up) {
	$lang = "l=en"; // определяем язык    
	$not_done = false;
	if (empty ( $_REQUEST ['my_login'] ) || empty ( $_REQUEST ['my_pass'] )) {
		echo "<b><center>Empty login/pass Netload.in.</center></b>\n";
		$mem = false;
	} else {
		?>
<div id=login width=100% align=center>Login to Netload.in</div>
<?php
		$mem = true;
		$Url = parse_url("http://netload.in/index.php");
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/index.php", "http://netload.in/index.php", 0, 0, 0, $_GET["proxy"],$pauth);
		preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cook = implode(';',$cookie);
		$post = array();
  $post["txtuser"] = $_REQUEST ['my_login'] ? $_REQUEST ['my_login'] : $premium_acc["netload"]["user"];
  $post["txtpass"] = $_REQUEST ['my_pass'] ? $_REQUEST ['my_pass'] : $premium_acc["netload"]["pass"];
  $post["txtcheck"] = "login";
  $post['txtlogin'] = '';
  $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/index.php", "http://netload.in/index.php", $cook, $post, 0, $_GET["proxy"],$pauth);
		is_page ( $page );
		//print_r($page);
		if (strpos ( $page, "we couldnt find user" )) {
			echo "<b><center>Error login to Netload.in.</center></b>\n";
		} else {
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cook = implode(';',$cookie);
			//$cook .= "; " . $lang;
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
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/index.php?id=1", 0, $cook, 0, 0, $_GET["proxy"],$pauth);
	?>
	<script>document.getElementById('info').style.innerHTML='Connected to netload, retrieving form...';</script>
<?php
	is_page ( $page );
	unset ( $post );
	$url_action = cut_str($page,'<form method="post" action="','"');
	$post['upload_hash'] = cut_str($page,'name="upload_hash" value="','"');
	$post['remote_file'] = 'http://';
	$post['directory_name'] = '';
	$url = parse_url ( $url_action );
	$cooks = explode(';',$cook);
	foreach ($cooks as $temp) {
		if (stristr($temp,'cookie_user')) {
			$cooks = $temp;
			break;
		}
	}
?>
	<script>document.getElementById('info').style.innerHTML='Uploading...';</script>
<?php
	$upfiles = upfile ( $url ["host"], $url ["port"] ? $url ["port"] : 80, $url ["path"] . ($url ["query"] ? "?" . $url ["query"] : ""), "http://netload.in/index.php?id=1", $cooks, $post, $lfile, $lname, "file" );
	
	?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	is_page ( $upfiles );
	$loc = "";
	preg_match('/ocation: (.*)/',$upfiles,$loc);
	$location = $loc[1];
	$Url = parse_url($location);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url['path'].'?'.$Url['query'], "http://netload.in/index.php?id=1", $cook, 0, 0, $_GET["proxy"],$pauth);
	
	is_present( $page, "Upload failed or the file doesn't meet our TOS.");
	//is_notpresent ( $upfiles, "downloadurl = '", "File not upload" );
	//preg_match ( '/\'(http:.*)\'/' ,$upfiles, $temp);
	//$download_link = $temp[1];
	$download_link = cut_str($page,'<input type="text" id="txtField" value="','"');
	$delete_link = cut_str($page,'<input type="text" id="txtField2" value="','"');
	
	
	//$download_link = cut_str ( $upfiles, "downloadurl = '", "'" );
    }
?>