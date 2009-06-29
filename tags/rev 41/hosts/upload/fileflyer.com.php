<?php
$continue_up=false;
$not_done=true;
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;Recipient Email Address<td>&nbsp;<input type=text name=remail value='' style="width:160px;" />&nbsp;</tr>
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
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
			$page = geturl("www.fileflyer.com", 80, "/", "", 0, 0, 0, "");
			is_page($page);
			
			$cookies = GetCookies($page);
			preg_match('/ASP.NET_SessionId=(.*?);/i', $cookies, $cook);
			$cookie = 'ASP.NET_SessionId=='.$cook[1];
			
			preg_match('/var txt *= *"(.*)"/i', $page, $up_host);
			preg_match('/__VIEWSTATE.*value="(.*?)"/i', $page, $up_vstat);
			preg_match('/__EVENTVALIDATION.*value="(.*?)"/i', $page, $up_even);
			preg_match('/action="(.*?)"/i', $page, $up_url);
			
			$action_url = $up_host[1].'Default.aspx?UploadID='.rand(10000000, 99999999);
			$url = parse_url($action_url);
			$post['__VIEWSTATE'] = $up_vstat[1];
			$post['__EVENTVALIDATION'] = $up_even[1];
			$post['R_email'] = $_REQUEST['remail'];
			$post['FieldCounter'] = 0;
?>
<script>document.getElementById('info').style.display='none';</script>

<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php 
            $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://www.fileflyer.com/', $cookie, $post, $lfile, $lname, "file1");
			is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 
			preg_match('/Location: *(.*)/i', $upfiles, $redir);
			$new_loc = trim($redir[1]);
			$Url = parse_url($new_loc);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://www.fileflyer.com/', $cookie, 0, 0, $_GET["proxy"],$pauth);
			
			preg_match('/HyperLink1".*href="(.*?)"/i', $page, $flink);
			preg_match('/RemovalLink.*href="(.*?)"/i', $page, $dlink);
			
			$download_link = 'http://www.fileflyer.com/'.$flink[1];
			$delete_link = 'http://www.fileflyer.com/'.$dlink[1];
	}
?>