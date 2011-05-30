<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 

/************************************************************
* Enter your default Login & Password below (if applicable) *
*************************************************************/
$fs_login = ""; // Username
$fs_pass = ""; // Password

$continue_up = false;

if ($_REQUEST ['action'] == "FORM" or (strlen($fs_login) > 0 && strlen($fs_pass) > 0)) {
	$continue_up = true;
	if (strlen($fs_login) > 0 && strlen($fs_pass) > 0) {
		$_REQUEST['my_login'] = $fs_login;
		$_REQUEST['my_pass'] = $fs_pass;
	}
} else {
	echo "<table border=1 style='width: 540px;' cellspacing=0 align=center>";
	echo "	<form method=post><input type=hidden name=action value='FORM' />";
	echo "	";
	echo "	<tr >";
	echo "	  <td colspan=4 align=center height=25px ><b>Enter Account</b> </td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td nowrap>&nbsp;Login		";
	echo "		<td>&nbsp;<input name=my_login value='' style='width: 160px;' />&nbsp;	";	
	echo "		<td nowrap>&nbsp;Password		";
	echo "		<td>&nbsp;<input type=password name=my_pass value='' style='width: 160px;' />&nbsp;	";
	echo "	</tr>	";
	echo "	<tr><td colspan=4 align=center><input type=submit value='Upload' /></tr>	";
	echo "</table>";
	echo "</form>";
	die;

}

if ($continue_up) {
	
	if (empty ( $_REQUEST ['my_login'] ) || empty ( $_REQUEST ['my_pass'] )) {
		html_error("Empty login/pass.");		
	}
	
	global $Referer;
	$loginUrl = "http://www.fileserve.com/login.php";
	$Referer = "http://www.fileserve.com/";	
	
	$post = array();
	$post["autoLogin"] = true;
	$post["loginUserName"] = $_REQUEST ['my_login'];
	$post["loginUserPassword"] = $_REQUEST ['my_pass'];
	$post["loginFormSubmit"] = true;
	//$page = $this->GetPage( $loginUrl, 0, $post, $Referer1 , 0, $_GET["proxy"]);
	
	$page = geturl("www.fileserve.com", 80, "/login.php", $Referer, 0, $post, 0, "");
	
	
	is_present($page, 'Username doesn\'t exist', 'Error - Username doesn\'t exist!');
	is_present($page, 'Wrong password', 'Error - Wrong password');
	
	//if (!stristr ( $page, "<h3>Premium</h3>" )) {
	//	html_error("Your account is not a premium account.");		
	//}
	$cookie = GetCookies( $page );

	
	$page = geturl("www.fileserve.com", 80, "/", "", $cookie, 0, 0, "");

	is_page($page);

	preg_match('/id="uploadHostURL" value="(.+?)"/', $page, $result);
	$uploadHostURL = $result[1];		
	
	
	preg_match('/id="userId" value="(.+?)"/', $page, $result);
	$userId = $result[1];		
	//echo "userId: ".$userId."<BR>";
	//$cookie = GetCookies( $page );
	$page = geturl("www.fileserve.com", 80, "/upload-track.php", $Referer,$cookie, 0, 0, "");

	
	if (!stristr($page,"sessionId")){
		html_error("Fail to upload!");
	}
	
	preg_match('/sessionId":"(.+?)"/', $page, $result);
	$sessionId = $result[1];
	
	print "<script>document.getElementById('info').style.display='none';</script>";

	$action_url = "http://".$uploadHostURL."/upload/upload.php?X-Progress-ID=".$sessionId;
	$path = "/upload/upload.php?X-Progress-ID=".$sessionId;
	
	$url = parse_url($action_url);
	$post = array();
	$post["affiliateId"] = "";	
	$post["subAffiliateId"] = "";
	$post["landingId"] = "";	
	$post["userId"] = $userId;
	$post["uploadSessionId"] = $sessionId;
	$post["uploadHostURL"] = $uploadHostURL;
	
	preg_match('/fs(.+?)u/', $uploadHostURL, $serverId);
	$post["serverId"] = $serverId[1];
	
	$upfiles=upfile($uploadHostURL,80,$path,$Referer, $cookie, $post, $lfile, $lname, "file");

	
	print "<div id=info width=100% align=center>Retrive file link</div>";
	
	$path = "/upload/progress.php?X-Progress-ID=".$sessionId."&callback=jsonp1275372927989&_=1275373001197";
	
	$page = geturl($uploadHostURL, 80, $path, $Referer,$cookie, 0, 0, "");	
	
	$post = array();

	$post["uploadSessionId[]"] = $sessionId;	

	$page = geturl("www.fileserve.com", 80, "/upload-result.php", $Referer,$cookie, $post, 0, "");	
	
	if (!stristr($page,"www.fileserve.com/file/")){
		html_error("Fail to upload!");
	}
	$key = cut_str($page, "www.fileserve.com/file/", "\n");
		
	
	$download_link = "http://www.fileserve.com/file/".$key;
	
}	
	
// written by charles 31/05/10
// added option to enter a default account (by Jueki @ 03/07/10)
?>
