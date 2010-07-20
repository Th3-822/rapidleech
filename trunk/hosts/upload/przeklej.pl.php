<?php
####### Free Account Info. ###########
$przeklej_username=""; //  Set you username
$przeklej_password=""; //  Set your password
##############################

$not_done=true;
$continue_up=false;
if ($przeklej_username & $przeklej_password){
	$_REQUEST['my_login'] = $przeklej_username;
	$_REQUEST['my_pass'] = $przeklej_password;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["przeklej.pl"]; ?></b></small></tr>
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
<?
            $usr=$_REQUEST['my_login'];
            $pass=$_REQUEST['my_pass'];
            $referrer="http://www.przeklej.pl/";
            $Url = parse_url("http://www.przeklej.pl/loguj");
			$post['login[login]'] = $usr;
			$post['login[pass]'] = $pass;
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$cookie =GetCookies($page);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
            is_notpresent($page,$usr,"Not logged in. Check your login details in przeklej.pl.php");
            $url_action="http://www.przeklej.pl/simple_upload" ;
			$Url = parse_url($url_action);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$tok=cut_str($page, 'name="token" value="', '"');
			$fpost['token'] = $tok;
	?>
<script>document.getElementById('info').style.display='none';</script>

		<table width=600 align=center>
			</td>
			</tr>
			<tr>
				<td align=center>
<?php
			$url = parse_url("http://www.przeklej.pl/dodaj_plik_form");
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$referrer, $cookie, $fpost, $lfile, $lname, "plik[plik]");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php



			$token=cut_str($upfiles, 'filetokens" value="', '"');
			$fileads=cut_str($upfiles, 'file[fileids]" value="', '"');
			unset($post);
			$post['filetokens'] = $token;
			$post['file[fileids]'] = $fileads;
			$post['privacy-status'] = "0";
			$post['file[haslo_pliki]'] = "";
			$post['email'] = "0";
			$post['file[email]'] = "";
			$post['directory-val'] = "0";
			$post['file[foldername]'] = "";
			$post['file[description]'] = "";
			$post['file[haslo]'] = "";
			$url = parse_url("http://www.przeklej.pl/dodaj_pliki");
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $referrer, $cookie, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
		//	is_notpresent($page,"Files Uploaded","Error upload file",0);
			$download_link="http://www.przeklej.pl/".cut_str($page, 'url=', '"');

			}

/*************************\
Written by kaox 14-jun-2009
Update by kaox 19-jul-2010
\*************************/
?>
