<?php
# Edit by VietPublic, http://www.tuoitrevnnet.com"
# RapidLeech Updated List : http://vietpublic.spaces.live.com
##########################################
$mega_login = ""; // login
$mega_pass = ""; // password
$mega_desc="Uploaded from rapidleech";  // Descriptions default
##########################################
$not_done = true;
$continue_up = false;
$cook = "";
if ($mega_login & $mega_pass) {
	$_REQUEST ['my_login'] = $mega_login;
	$_REQUEST ['my_pass'] = $mega_pass;
	$_REQUEST ['action'] = "FORM";
	echo "<b><center>Use Default Megaupload.com login/pass.</center></b>\n";
}

if ($_REQUEST ['action'] == "FORM")
	$continue_up = true; else {
	?>
<table border=1 style="width: 540px;" cellspacing=0 align=center>
	<form method=post><input type=hidden name=action value='FORM' />
	
	<tr >
	  <td colspan=4 align=center height=25px ><b>	Enter Free or Premium Account</b> </td>
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
		echo "<b><center>Empty login/pass Megaupload.com. Use <span style='color:red'>FREE</span> Megaupload Account.</center></b>\n";
		$mem = false;
	} else {
		?>
<div id=login width=100% align=center>Login to Megaupload.com</div>
<?php
		$mem = true;
		$post = array();
  $post['login'] = 1;
  $post['redir'] = 1;
  
  $post["username"] = $_REQUEST ['my_login'] ? $_REQUEST ['my_login'] : $premium_acc["megaupload"]["user"];
  $post["password"] = $_REQUEST ['my_pass'] ? $_REQUEST ['my_pass'] : $premium_acc["megaupload"]["pass"];
  $Url = parse_url("http://megaupload.com");
  $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, "/?c=login", 0, 0, $post, 0, $_GET["proxy"],$pauth);
		is_page ( $page );
		//print_r($page);
		if (strpos ( $page, "Invalid nickname" )) {
			echo "<b><center>Error login to Megaupload.com. Use <span style='color:red'>FREE</span> Megaupload Account.</center></b>\n";
		} else {
			$cook = GetCookies ( $page );
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
	$page = geturl ( "www.megaupload.com", 80, "/", 0, $cook, 0, 0, "" );
	?>
	<script>document.getElementById('info').style.innerHTML='Connected to megaupload, retrieving form...';</script>
<?php
	is_page ( $page );
	unset ( $post );
	preg_match('/="multipart\/form-data" action="(.*)" target="uploadframe"/',$page,$temp);
	$url_action = $temp[1];
	$sessionid = cut_str ( $page, 'name="sessionid" value="', '"' );
	//$UPLOAD_IDENTIFIER = cut_str ( $page, 'name="UPLOAD_IDENTIFIER" value="', '"' );
	
	//if (! $url_action || ! $sessionid || ! $UPLOAD_IDENTIFIER) {
	//	html_error ( "Error retrive upload id" . pre ( $page ) );
	//}
	
	//$post ["sessionid"] = $sessionid;
	//$post ["UPLOAD_IDENTIFIER"] = $UPLOAD_IDENTIFIER;
	//$post ["accept"] = 1;	
	$post ["multimessage_0"] = $_REQUEST ['message'];	
	$post ['trafficurl'] = "http://";
	
	$url = parse_url ( $url_action );
?>
	<script>document.getElementById('info').style.innerHTML='Uploading...';</script>
<?php
	$upfiles = upfile ( $url ["host"], $url ["port"] ? $url ["port"] : 80, $url ["path"] . ($url ["query"] ? "?" . $url ["query"] : ""), "http://www.megaupload.com/", $cook, $post, $lfile, $lname, "multifile_0" );
	
	?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	is_page ( $upfiles );
	
	//is_notpresent ( $upfiles, "downloadurl = '", "File not upload" );
	preg_match ( '/\'(http:.*)\'/' ,$upfiles, $temp);
	$download_link = $temp[1];
	
	//$download_link = cut_str ( $upfiles, "downloadurl = '", "'" );
    }
?>