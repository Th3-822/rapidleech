<?php
####### Account Info. ###########
$freakshare_user = "";
$freakshare_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($freakshare_user & $freakshare_pass){
	$_REQUEST['fk_user'] = $freakshare_user;
	$_REQUEST['fk_pass'] = $freakshare_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Automatic Login Freakshare.com</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
					<script>document.getElementById('info').style.display='none';</script>
                    <div id='info' width='100%' align='center' style="font-weight:bold; font-size:16px">LOGIN</div> 
    <table border=0 style="width:270px;" cellspacing=0 align=center>
        <form method="post">
        <input type='hidden' name='action' value='FORM' />
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='fk_user' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='fk_pass' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td colspan=2 align=center><input type='submit' value='Upload' /></tr>
            <tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["freakshare.com"]; ?></b></small></tr>
        </form>
    </table>
<?
}

if ($continue_up)
	{
		$not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('info').style.display='none';</script>
            <div id='info' width='100%' align='center'>Login to Freakshare.com</div>
<?php
				if (!empty($_REQUEST['fk_user']) && !empty($_REQUEST['fk_pass'])){
				$Url=parse_url('http://freakshare.com/login.html');
				$post["user"]=$_REQUEST['fk_user'];
				$post["pass"]=$_REQUEST['fk_pass'];
				$post["submit"]="Login";
				$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://freakshare.com/login.html", 0, $post, 0, $_GET["proxy"], $pauth);
				is_page($page);	
				$cookies=GetCookies($page);
				preg_match("#login=([^=]+)#", $cookies, $login);
				if(empty($login[1])){
					html_error('Wrong Username or Password!');
				}
				}else{
		html_error ('Error, user and/or password is empty, please go back and try again!');
	}
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$Url=parse_url('http://freakshare.com/');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://freakshare.com/login.html", $cookies, $post, 0, $_GET["proxy"], $pauth);
			if(!preg_match('#action="([^"]+)"#', $page, $up)){
				html_error('Cannot get url action upload.');
			}
			if(!preg_match('#id="progress_key"[\r|\n|\s]+value="([^"]+)"#', $page, $key)){
				html_error('Cannot get user progress key.');
			}
			if(!preg_match('#id="usergroup_key"[\r|\n|\s]+value="([^"]+)"#', $page, $group_key)){
				html_error('Cannot get user group key.');
			}
			if(!preg_match('#name="UPLOAD_IDENTIFIER"[\r|\n|\s]+value="([^"]+)"#', $page, $UPLOAD_IDENTIFIER)){
				html_error('Cannot get upload ID.');
			}
			$uuid = uuid();
			$url = $up[1].'?X-Progress-ID='.$uuid;
			$url = parse_url($url);
			$post["APC_UPLOAD_PROGRESS"] = $key[1];
			$post["APC_UPLOAD_USERGROUP"] = $group_key[1];
			$post["UPLOAD_IDENTIFIER"]= $UPLOAD_IDENTIFIER[1];
			$rand = '0.'.uid();
			$mt = $rand*1000000;
			$requestkey = substr(str_replace('.', '', microtime(true)), 0, -1).'-'.str_replace(',', '',number_format($mt, 10));
			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://freakshare.com/", 0, $post, $lfile, $lname, "file[1]");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php

			is_page($upfiles);
			$locat=trim(cut_str($upfiles,'Location: ',"\n"));
			$page = 'http://freakshare.com/request/jsonreq.php?url='.str_replace('upload.php', '',$up[1]).'&XProgressID='.$uuid.'&requestkey='.$requestkey;
			$url = parse_url($page);
			$page = geturl($url["host"],  80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://freakshare.net/', $cookies, 0, 0, $_GET["proxy"],$pauth);
			if(preg_match('#"status" : (\d+)#', $page, $status)){
				if($status[1] != '302'){
					html_error('Error in upload.');
				}
			}
			$Url=parse_url($locat);
			$page = geturl($Url["host"],  80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://freakshare.net/', $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			if(!preg_match('#type="text"[\r|\n|\s]+value="([^"]+)"#', $page, $dl)){
				html_error('Cannot get download link.');
			}
			$download_link = $dl[1];
			if(!preg_match('#delete\/([^"]+)"#', $page, $dlet)){
				html_error('Cannot get delete link.');
			}
			$delete_link= 'http://freakshare.net/delete/'.$dlet[1];
}
function uid(){
				$nu = "0123456789";
				for($i=0; $i < 19; $i++){
				$rand .= $nu{mt_rand() % strlen($nu)};
				}
				return $rand;
				//function by simplesdescraga 05/02/2012
	}
function uuid(){
				$ext = "0123456789";
				$hex = "0123456789abcdef";
				$let = str_split($hex,1);
				$contbase = strlen($hex);
				$comple = '';
						for($i=0; $i < 46; $i++){
						$rand .= $ext{mt_rand() % strlen($ext)};
						}
				$base = $rand;
				for ($i = 0; $i < 32; $i++) {
					$comple = $let[bcmod($base,$contbase)].$comple;
					$base = bcdiv($base,$contbase,0);
				}
			return $comple;
	//function by simplesdescarga 02/02/2012
	}
	
//working by SD-88 18/03/2012
?>