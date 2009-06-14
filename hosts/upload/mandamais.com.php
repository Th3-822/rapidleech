<?php 

####### Free Account Info. ###########
$mandamais_username=""; //  Set you username
$mandamais_password=""; //  Set your password
##############################

$not_done=true;
$continue_up=false;
if ($mandamais_username & $mandamais_password){
	$_REQUEST['my_login'] = $mandamais_username;
	$_REQUEST['my_pass'] = $mandamais_password;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["mandamais.com"]; ?></b></small></tr>
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
            $referrer="http://www.mandamais.com.br/";
            $usr=$_REQUEST['my_login'];
            $pass=$_REQUEST['my_pass'];
            $Url = parse_url("http://www.mandamais.com.br/validar.asp");  
			$post['login'] = $usr;
			$post['senha'] = $pass;
			$post['imageField2.x'] = rand(1,150);
            $post['imageField2.y'] = rand(1,150);
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
		    preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
            $cook = $temp[1];
            $cookie = implode(';',$cook);	
			$Url = parse_url("http://www.mandamais.com.br/discovirtual/");
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
            is_notpresent($page, $usr,"Not logged in. Check your login details in ".$page_upload["mandamais.com"] );
			

			

            $Url = parse_url($referrer."scripts_upload/upload_file.js");  
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			$tid=time() % 1000000000;
			$quer=cut_str($page, 'MyForm.action = "', '"');
			$url_action = "http://".$Url["host"].$quer.$tid;
			$fpost['descricao'] = $lname;
			$fpost['categoria'] = "6";
			$fpost['tipo_p'] = "1";
			$fpost['pasta_cliente'] = "raiz";
			
	?>
<script>document.getElementById('info').style.display='none';</script>

		<table width=600 align=center>
			</td>
			</tr>
			<tr>
				<td align=center>
<?php		
					
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$referrer, $cookie, $fpost, $lfile, $lname, "arquivo");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	

			is_notpresent($upfiles,"processa_upload","Error upload file",0);
			$Url=parse_url("http://www.mandamais.com.br/scripts_upload/processa_upload.asp");
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			preg_match('/\/download\/.+/i', $page, $redir);
			$down=rtrim("http://www.mandamais.com.br".$redir[0]);
			$Url=parse_url($down);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);			
			$download_link=$down;
			}
			// written by kaox 01/06/2009
	
?>