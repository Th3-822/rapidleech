<?php
/******************mediafire.com****************************\
mediafire.com Member Upload Plugin
WRITTEN by Raj Malhotra on 06 Feb 2011
\******************mediafire.com****************************/

####### Free Account Info. ###########
$mediafire_login = "";
$mediafire_pass = "";
######################################

processUpload($mediafire_login, $mediafire_pass, $lfile, $lname);

function processUpload( $mediafire_login, $mediafire_pass, $lfile, $lname )
{
	global $download_link, $delete_link, $page_upload;
	$continue_up=false;
	if ($mediafire_login & $mediafire_pass)
	{
		$_REQUEST['my_login'] = $mediafire_login;
		$_REQUEST['my_pass'] = $mediafire_pass;
		$_REQUEST['action'] = "FORM";
		echo "<b><center>Use Default login/pass.</center></b>\n";
	}
	
	if ( $_REQUEST ['action'] == "FORM" )
	{
		$continue_up = true; 
	} 
	else 
	{
	?>
		<table border="1" style="width: 540px;" cellspacing="0" align="center" >
			<form method="post">
				<input type="hidden" name="action" value='FORM' />
			
				<tr>
				  <td colspan="4" align="center" height="25px" ><b> Enter Member Account </b> </td>
				</tr>
				<tr>
					<td nowrap>&nbsp;Email*</td>
					<td>&nbsp;<input name="my_login" value='' style="width: 160px;" />&nbsp;</td>
					<td nowrap>&nbsp;Password*</td>
					<td>&nbsp;<input type="password" name="my_pass" value='' style="width: 160px;" />&nbsp;</td>
				</tr>
				<tr>
					<td colspan="4" align="center">&nbsp;<b>You can set it as default in <b><?php echo $page_upload["mediafire.com_member"]; ?></b>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="4" align="center">
						<input type="submit" value='Login' />
					</td>
				</tr>
			</form>
		</table>
	<?php
	}
	
	if ( $continue_up ) 
	{ 
		$mediafire_com_login = $_REQUEST ['my_login'];
		$mediafire_com_pass = $_REQUEST ['my_pass'];
	?>
		<table width="100%" align="center">
			<tr>
				<td align="center">
				<div id="login" width="100%" align="center">Login to mediafire.com</div>
	<?php
		$cookies = login( $mediafire_com_login, $mediafire_com_pass );
	?>
		<script>document.getElementById('login').style.display='none';</script>
		<div id="info" width="100%" align="center">Retrive upload ID</div>
	<?php
		
		$matc = array();
		preg_match("/ukey=[^ ;\r\n]+/",$cookies,$matc);
		$ukey = $matc[0];
		preg_match("/user=[^ ;\r\n]+/",$cookies,$matc);
		$user = $matc[0];
		$rnn = rand(10000,99999);
		
		$Href = "http://www.mediafire.com/basicapi/uploaderconfiguration.php?" . $rnn;
		$Referer = "http://www.mediafire.com/myfiles.php";
		$page = GetPage( $Href, $cookies, 0, $Referer );
		
		$track= cut_str ( $page ,'<trackkey>' ,'</trackkey>' );
		$uploadkey= cut_str ( $page ,'<folderkey>' ,'</folderkey>' );
		$MFULConfig = cut_str ( $page ,'<MFULConfig>' ,'</MFULConfig>' );
		
		$Referer = $Href;
		$Href = "http://www.mediafire.com/douploadtoapi/?track=" . $track . "&" . $ukey . "&" . $user . "&uploadkey=" . $uploadkey . "&upload=0";
		
		?>
		<script>document.getElementById('info').style.display='none';</script>
		<?php
		
		$post = array();
		$post["Filename"]=$lname;
		$post["Upload"]="Submit Query";
		$upagent = "Shockwave Flash";
		$url = parse_url( $Href );
		$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $Referer, $ukey, $post, $lfile, $lname, "Filedata",0,0,0,$upagent);
		is_page($upfiles);
		
		?>
		<script>document.getElementById('progressblock').style.display='none';</script>
		<div id="final" width="100%" align="center">Get final code</div>
		<?php
		
		$key = cut_str($upfiles,"<key>","</key>");
		if (!$key)
		{
			html_error("Error retrive final id");
		}
		$error = true;
		$Referer = "http://www.mediafire.com/myfiles.php";
		for ( $i=1; $i<12; $i++ )
		{
			sleep(4);
			echo $i;
			$Href = "http://www.mediafire.com/basicapi/pollupload.php?key=" . $key . "&MFULConfig=" . $MFULConfig;
			$page = GetPage( $Href, $cookies, 0, $Referer );
			
			if (stristr($page,"<fileerror>13</fileerror>"))
			{
				html_error("File already existent!  for your link get it in mediafire member section");					
			}
			if (strstr($page,"No more requests"))
			{
				$error = false; 
				break;
			}
			echo ", ";
		}
		
		if ($error == true)
		{
			html_error("Error verification time out!");
		}
		
		$links_up_file = cut_str($page,'<quickkey>','</quickkey>');

		if (!$links_up_file)
		{
			html_error("Error retrive upload links!");
		}

		$download_link = 'http://www.mediafire.com/?' . $links_up_file;
	}
}

function login( $user, $password )
{
	$baseurl = "http://www.mediafire.com/";
	$page = GetPage( $baseurl );
	$cookie = GetCookies($page);
	
	$loginURL = "http://www.mediafire.com/dynamic/login.php";
	$Referer = "http://www.mediafire.com/";
	$post = array();
	
	$post['login_email'] = $user;
	$post['login_pass'] = $password;
	$post['submit_login.x'] = rand(0,90);
	$post['submit_login.y'] = rand(0,90);
	
	$page = GetPage( $loginURL, $cookie, $post, $Referer );
	
	$et = trim ( cut_str( $page, 'var et= ', ';' ) );
	if ( $et != 15 )
	{
		html_error( 'Login Error: Invalid email address or password. Please try again.', 0);
	}

	$cookie = $cookie . '; ' . GetCookies($page);
	return $cookie;
}

/**
 * You can use this function to retrieve pages without parsing the link
 * 
 * @param string $link The link of the page to retrieve
 * @param string $cookie The cookie value if you need
 * @param array $post name=>value of the post data
 * @param string $referer The referer of the page, it might be the value you are missing if you can't get plugin to work
 * @param string $auth Page authentication, unneeded in most circumstances
 */
function GetPage($link, $cookie = 0, $post = 0, $referer = 0, $auth = 0) {
	global $pauth;
	if (!$referer) {
		global $Referer;
		$referer = $Referer;
	}
	$Url = parse_url(trim($link));
	$page = geturl ( $Url ["host"], $Url ["port"] ? $Url ["port"] : 80, $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : ""), $referer, $cookie, $post, 0, $_GET ["proxy"], $pauth, $auth );
	is_page ( $page );
	return $page;
}

/******************mediafire.com****************************\
mediafire.com Member Upload Plugin
WRITTEN by Raj Malhotra on 06 Feb 2011
\******************mediafire.com****************************/
?>