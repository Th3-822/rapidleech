<?php
####### Account Info. ###########
$fileape_login = '';
$fileape_pass = '';
##############################

				$not_done=true;
				$continue_up=false;
				if ($fileape_login && $fileape_pass){
					$_REQUEST['fileape_login'] = $fileape_login;
					$_REQUEST['fileape_pass'] = $fileape_pass;
					$_REQUEST['action'] = "FORM";
					echo "<b><center>Use Default login/pass.</center></b>\n";
				}
				if ($_REQUEST['action'] == "FORM")
					$continue_up=true;
				else{
?>
					<script>document.getElementById('info').style.display='none';</script>
            <div id='info' width='100%' align='center' style="font-weight:bold; font-size:16px">LOGIN</div>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;User*<td>&nbsp;<input type=text name=fileape_login style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=fileape_pass style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
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
<div id=login width=100% align=center></div> 
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Login to Fileape.com</div> 
<?php
			if ($_REQUEST['action'] == "FORM" && !empty($_REQUEST['fileape_login']) && !empty($_REQUEST['fileape_pass'])) {
			$post = array();
		    $post['username'] = trim($_REQUEST['fileape_login']);
		    $post['password'] = trim($_REQUEST['fileape_pass']);
			$post['pakistan_should_be_disconnected_from_the_rest_of_the_internet'] = 'immediately';
			$url=parse_url('http://fileape.com/?act=login');
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://fileape.com/", 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			$cookies = GetCookies($page);
			if(empty($cookies)){
				html_error('there was an error. entered the wrong username or password?');
			}
			}else{
				html_error('User or password is empty, check and try again');
				}
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$page = geturl("fileape.com", 80, "/", 0, $cookies, 0, 0, $_GET["proxy"]);
			is_page($page);
			if(!preg_match('#form[\r|\n|\s]+action="([^"]+)"#', $page, $act)){
				html_error('Cannot get form action.', 0);
			}
			if(!preg_match('#name="userid"[\r|\n|\s]+value="([^"]+)"#', $page, $userid)){
				html_error('Cannot get form userid.', 0);
			}
			if(!preg_match('#name="users"[\r|\n|\s]+value="([^"]+)"#', $page, $users)){
				html_error('Cannot get form users.', 0);
			}
			$url = parse_url($act[1]);
			$post["raw"]= '0';
			$post["userid"]= $userid[1];
			$post["users"]= $users[1];
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://fileape.com/", $cookies, $post, $lfile, $lname, "file");
			is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			/*This is a countdown timer, use it persists error: line 91.
			is_page($upfiles);
            insert_timer( 10, "Wait for Redirect Download Link.","",true );
			*/
			preg_match("#[\r|\n|\s]+Location:[\r|\n|\s]+([^']+)#", $upfiles, $dlink);
			$link = split ('[=]', $dlink[1]);
			if ($link[2] != 2){
				if(!empty($link[3])){
					$download_link = 'http://fileape.com/dl/'.$link[3];
				}else{
					html_error ("Didn't find download link!");
				}
			}else{
				html_error ("The file you submitted was too small or submitted improperly. Please try again.");
			}
	}
/**
written by simplesdescarga 14/01/2012
**/   
?>