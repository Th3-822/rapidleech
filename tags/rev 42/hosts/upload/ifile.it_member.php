<?php

//Input your ifile.it unique API KEY
$ifile_apikey = '';


 /////////////////////////////////////////////////
$not_done=true;
$continue_up=false;
if ($ifile_apikey)
{
	$_REQUEST['my_apikey'] = $ifile_apikey;
	$_REQUEST['action'] = "FORM";
	echo "<center><b>Use Default login/pass...</b></center>\n";
}
if ($_REQUEST['action'] == "FORM")
{
	$continue_up=true;
}
else
{
	echo <<<EOF
<div id=login width=100% align=center>Login to Site</div>
<table border=0 style="width:350px;" cellspacing=0 align=center>
	<form method=post>
		<input type=hidden name=action value='FORM' />
		<tr><td nowrap>&nbsp;API Key*</td><td>&nbsp;<input type=text name=my_apikey value='' style="width:350px;" />&nbsp;</td></tr>
		<tr><td colspan=2 align=center><input type=submit value='Upload'></td></tr>
	</form>
</table>
EOF;
}

if ($continue_up)
{
	$not_done = false;

	if ( empty($_REQUEST['my_apikey'])) html_error('No ifile.it api key provided', 0);
	echo "<script>document.getElementById('login').style.display='none';</script>";
?>
<center>
<?php
//////////////////////////		EDIT FROM HERE DOWN		///////////////////////////////////////

	echo "<div id=info width=100% align=center>Retrieve upload ID</div>";
	$Url = parse_url('http://ifile.it/upload:api_fetch_upload_server?apikey=' . trim($_REQUEST['my_apikey']));
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"], $pauth);
	is_page($page);
	is_notpresent($page, 'ok', 'An unknown error occured.');
	if (!preg_match('%"server_id":"(\d+)"}%U', $page, $sid)) html_error('Error getting upload server id');
	$server_id = $sid[1];

	echo "<script>document.getElementById('info').style.display='none';</script>";
	$url = parse_url('http://s' . $server_id . '.ifile.it/upload?apikey=' . trim($_REQUEST['my_apikey']) . '&response=text');
	$post = array();
	$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $post, $lfile, $lname, "Filedata");
	is_page($upfiles);
	is_notpresent($upfiles, 'ok', 'File upload failed!');
	echo "<script>document.getElementById('progressblock').style.display='none';</script>";

	$upfile_content = explode("\r\n\r\n", $upfiles);
	$upfile_content = explode("\n", trim($upfile_content[1]));

	foreach ($upfile_content as $upinfo)
	{
		list($var, $val) = explode(': ', $upinfo);
		$upinfos[$var] = $val;
	}

	$download_link = $upinfos['url'];
	$stat_link = $upinfos['file_md5'];
}
//szalinski 22-Aug-2009
?>
</center>