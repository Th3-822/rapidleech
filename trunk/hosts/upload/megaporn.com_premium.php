<?php

####### Account Info. ###########
$mega_login = ""; // login
$mega_pass = ""; // password
##############################

$not_done=true;
$continue_up=false;
if ($mega_login && $mega_pass){
	$_REQUEST['my_login'] = $mega_login;
	$_REQUEST['my_pass'] = $mega_pass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["megaporn.com"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to megaporn.com</div>
<?php
	                $post = array();
	                $post['login'] = '1';
	                $post['username'] = trim($_REQUEST['my_login']);
	                $post['password'] = trim($_REQUEST['my_pass']);
	                $page = geturl("megaporn.com", 80, "/?c=account", 0, 0, $post, 0, $_GET["proxy"], $pauth);
	                is_page($page);
                        is_present($page, 'Username and password do not match. Please try again!', 'Error logging in - are your logins correct!');
                        $cookie = GetCookies($page);
	                $page = geturl("megaporn.com", 80, "/?", "http://www.megaporn.com", $cookie, 0, 0, "");
                        is_page($page);
                        
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 		
                        $server = cut_str($page, 'flashvars.server = "','";');
                        $s = rndNum(6);
                        $rand = rndNum(21);                  
                        $ID= '0'.time().$rand;
                        $upload_form = $server."upload_done.php?UPLOAD_IDENTIFIER=$ID&user=undefined&s=$s";
                        $url = parse_url($upload_form);

?>

<?php 	
			$fpost = array();
			$fpost["Filename"] = $lname;
			$fpost["message"] = 'go to norulesteam.us';
			$fpost["trafficurl"] = 'undefined';
			$fpost["user"] = 'undefined';
			$fpost["hotlink"] = '0';
			$fpost["Upload"] = 'Submit Query';		
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://www.megaporn.com",$cookie, $fpost, $lfile, $lname, "Filedata");
?>

<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
                        insert_timer( 5, "Wait for Redirect Download Link.","",true );
                        preg_match('/downloadurl *= *\'(.*?)\'/i', $upfiles, $dllink);
                        $download_link = $dllink[1];

			
	}
function rndNum($lg){
        $str="0123456789"; 
	for ($i=1;$i<=$lg;$i++){
	$st=rand(1,9);
	$pnt.=substr($str,$st,1);}
        return $pnt;
        }

//VinhNhaTrang_01.12.2010
?>