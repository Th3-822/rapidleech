<?php 

####### Free Account Info. ###########
$filefront_username=""; //  Set you username
$filefront_password=""; //  Set your password
##############################

$not_done=true;
$continue_up=false;
if ($filefront_username & $filefront_password){
	$_REQUEST['my_login'] = $filefront_username;
	$_REQUEST['my_pass'] = $filefront_password;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["filefront.com"]; ?></b></small></tr>
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
            $referrer="http://hosted.filefront.com/".$usr; 
            $Url = parse_url("http://signup.filefront.com/");
			$post['uploadID'] = "";
			$post['existingUser'] = "1";	
            $post['redirectTo'] = $referrer;
			$post['pageID'] = "49";
			$post['loginUser'] = $usr;
			$post['loginPass'] = $pass;
			$post['x'] = rand(1,50);
            $post['y'] = rand(1,20);
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
            is_present($page,"signup","Not logged in. Check your login details in filefront.com.php");
		    preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
            $cook = $temp[1];
            $cookie = implode(';',$cook);	
			$Url = parse_url("http://uploadhosted.filefront.com/");
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);			
			$formact=cut_str($page, '<div class="rightColUpload">', '</div>');
			$url_action=cut_str($formact, 'action="', '"');
			$fpost['UPLOAD_IDENTIFIER'] = cut_str($page, 'UPLOAD_IDENTIFIER value="', '"');
			$fpost['FL'] = "k";
			$fpost['upload'] = "1";
			$fpost['game'] = "";		
			

			
	?>
<script>document.getElementById('info').style.display='none';</script>

		<table width=600 align=center>
			</td>
			</tr>
			<tr>
				<td align=center>
<?php		
			$referrer="http://uploadhosted.filefront.com/";
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$referrer, $cookie, $fpost, $lfile, $lname, "upload_formdata[0]");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	

			preg_match('/location: *(.*)/i', $upfiles, $redir);
			$Href = rtrim($redir[1]);
			$Url = parse_url($Href);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_present($page,"Duplicate file","You attempted to upload a file that already exists in your directory. Please rename the file or delete the existing file.",0);
			is_notpresent($page,"Upload Successful!","Error upload file",0);
			preg_match('/http:\/\/files\.filefront\.com\/\w+/i', $page, $down);

			$download_link=$down[0];
			}
			
			// written by kaox 09/06/2009
?>