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
	echo "<b><center>Use Default Uploadbox.com login/pass.</center></b>\n";
}

if ($_REQUEST ['action'] == "FORM")
	$continue_up = true; else {
	?>
<table border=1 style="width: 540px;" cellspacing=0 align=center>
	<form method=post><input type=hidden name=action value='FORM' />
	
	<tr >
	  <td colspan=4 align=center height=25px ><b>Enter Free Account</b> </td>
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
	$not_done = false;
	if (empty ( $_REQUEST ['my_login'] ) || empty ( $_REQUEST ['my_pass'] )) {
		echo "<b><center>Empty login/pass UploadBox.com.</center></b>\n";
		$mem = false;
	} else {
		?>
<div id=login width=100% align=center>Login to UploadBox.com</div>
<?php
		$mem = true;
		$Url = parse_url("http://uploadbox.com/");
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/", "http://uploadbox.com/en/?ac=login", 0, 0, 0, $_GET["proxy"],$pauth);
		preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cook = implode(';',$cookie);
  $login = $_REQUEST ['my_login'] ? $_REQUEST ['my_login'] : $premium_acc["netload"]["user"];
  $passwd = $_REQUEST ['my_pass'] ? $_REQUEST ['my_pass'] : $premium_acc["netload"]["pass"];
  $ac = "auth";
  $back = '';
  $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/?login=$login&passwd=$passwd&ac=$ac&back=$back", "http://uploadbox.com/en/?ac=login", $cook, 0, 0, $_GET["proxy"],$pauth);
		is_page ( $page );
		//print_r($page);
		if (strpos ( $page, "Wrong password" )) {
			echo "<b><center>Error login to UploadBox.com.</center></b>\n";
		} else {
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
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/?ac=upload", 0, $cook, 0, 0, $_GET["proxy"],$pauth);
	?>
	<script>document.getElementById('info').style.innerHTML='Connected to netload, retrieving form...';</script>
<?php
	is_page ( $page );
	unset ( $post );
	preg_match('/UploadF"\)\.action = "(.+)"/i',$page,$url_action);
	$url_action = $url_action[1];
	$cooks = $cook;
	$post['agree']='1';
	$post['file_0_descr']=$descript;
	$post['xmode']='1';
	$post['pbmode']='inline2';
	$post['css_name']='';
	$post['tmpl_name']='';
	$ids=rand(100000,999999).rand(100000,999999);
	$url = parse_url($url_action.$ids);
?>
	<script>document.getElementById('info').style.innerHTML='Uploading...';</script>
<?php
	$upfiles = upfile ( $url ["host"], $url ["port"] ? $url ["port"] : 80, $url ["path"] . ($url ["query"] ? "?" . $url ["query"] : ""), "http://uploadbox.com/?ac=upload", $cooks, array(), $lfile, $lname, "filepc" );
	
	?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
		is_page($upfiles);
	    preg_match('/Location:.+?\\r/i', $upfiles, $loca);
        $redir = rtrim($loca[0]);
        preg_match('/http:.+/i', $redir, $loca);
		
//$tmp='http://progress3.uploadbox.com/upload_status.cgi?uid='.$ids.'&nfiles=1&xmode=1&lang=en&files=:"'.$lname.'"&inline=1';
	
		$Url=parse_url($loca[0]);
//$tmp1 = $tmp;
        $page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://uploadbox.com/?ac=upload', $cook, 0, 0, $_GET["proxy"],$pauth);
		/*
		$rnd="0.".rand(100000,999999).rand(100000,999999);
		$tmp='http://progress3.uploadbox.com/upload_status.cgi?uid='.$ids.'&ajax2=1&num=0&lang=en&rnd='.$rnd;
		$Url=parse_url($tmp);
		$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $tmp1, $cook, 0, 0, $_GET["proxy"],$pauth);
		is_page($page);
		is_notpresent ($page,'saved successfully','File not upload');
		$tmp=cut_str($page,"<a href='","'");
		if (!$tmp) html_error ('Error get url');
		*/
		$download_link=cut_str($page,"[URL=","/]");
		preg_match('/http:\/\/uploadbox.com\/delete\/\w+/i',$page,$del);
		$delete_link=$del[0];
    }
	// fixed by kaox 22/06/2009
?>