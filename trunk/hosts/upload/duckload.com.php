<?php 

####### Free Account Info. ###########
$duckload_username=""; //  Set you username
$duckload_password=""; //  Set your password
##############################

$not_done=true;
$continue_up=false;
if ($duckload_username & $duckload_password){
	$_REQUEST['my_login'] = $duckload_username;
	$_REQUEST['my_pass'] = $duckload_password;
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
<tr><td nowrap>&nbsp;Username*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["duckload.com"]; ?></b></small></tr>
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
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
            $rnd=time().rand(100,999);
            $usr=$_REQUEST['my_login'];
            $pass=$_REQUEST['my_pass'];
            $referrer="http://www.duckload.com/Members/Login"; 
            $Url = parse_url("http://www.duckload.com/api/public/login&user=$usr&pw=$pass&fmt=json&source=TOPNAV&callback=jsonp$rnd");
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, 0, 0, 0, $_GET["proxy"],$pauth);
	    is_page($page);

            is_present($page,"You have entered an incorrect password!");  
            is_present($page,"This account does not exist!"); 

            $cookie =GetCookies($page);
            $Url = parse_url("http://www.duckload.com/Members/");
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://www.duckload.com/Members/Login", $cookie, 0, 0, $_GET["proxy"],$pauth);
            is_page($page) ;
           
            preg_match('%<form action="(.*)" onsubmit="%i', $page, $match);
            $url_action = $match[1];  
            $ID = cut_str($url_action, "Progress-ID=","\r");
            $MAX_FILE_SIZE = cut_str($page, 'name="MAX_FILE_SIZE" value="','" />');
            $uid = cut_str($page, 'name="uid" value="','" />');
	?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td>
</tr>
<tr>
<td align=center>
<?php		
			$fpost = array(
			'MAX_FILE_SIZE' => $MAX_FILE_SIZE,
                        'uid'          => $uid);
			$url = parse_url($url_action);
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://www.duckload.com/Members/", $cookie, $fpost, $lfile, $lname, "file1");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
                        $Url=parse_url("http://www.duckload.com/api/links.php?id=$ID");
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://www.duckload.com/", $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
                       
                        preg_match('/<input type="text" name="dllink" class="config_input" style="width:90%" value="(.*)"/i', $page, $match);
                        $download_link = $match[1];
                        if (!$match[1]) html_error('Error getting return url');
                        preg_match('/<input type="text" name="dellink" class="config_input" style="width:90%" value="(.*)"/i', $page, $match);
                        $delete_link = $match[1];;		
}
			
//written by VinhNhaTrang 27/11/2010
?>