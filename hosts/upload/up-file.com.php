<?php 

####### Free Account Info. ###########
$upfile_username=""; //  Set you username
$upfile_password=""; //  Set your password
##############################

$not_done=true;
$continue_up=false;
if ($upfile_username & $upfile_password){
	$_REQUEST['my_login'] = $upfile_username;
	$_REQUEST['my_pass'] = $upfile_password;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["up-file.com"]; ?></b></small></tr>
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
            $referrer="http://up-file.com/ftp/upload_files.php";
            $usr=$_REQUEST['my_login'];
            $pass=$_REQUEST['my_pass'];
            $Url = parse_url("http://up-file.com/tmpl/login.php");  
			$post['log'] = $usr;
			$post['pas'] = $pass;
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://up-file.com/", 0, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);

            $cookie = GetCookies($page);	
			$Url = parse_url($referrer);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://up-file.com/page/register.php", $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
            is_notpresent($page, $usr."!","Not logged in. Check your login details in ".$page_upload["mandamais.com"] );
			$upurl=cut_str($page,'multipart/form-data" action="','"');



			$owner = cut_str($page,'"owner" type="hidden" id="owner" value="','"');
			$pin = cut_str($page,'e="pin" type="hidden" id="owner" value="','"');
			$base = cut_str($page,'="base" type="hidden" id="owner" value="','"');
			$host = cut_str($page,'="host" type="hidden" id="owner" value="','"');
			$tmpl_name = cut_str($page,'t type="hidden" name="tmpl_name" value="','"');

            unset($post);
			$post['xmode']='1';
			$post['pbmode']='inline2';
			$post['owner']=$owner;
			$post['pin']=$pin;
			$post['base']=$base;
			$post['host']=$host;
			$post['css_name']='';
			$post['tmpl_name']=$tmpl_name;

		
	?>
<script>document.getElementById('info').style.display='none';</script>

		<table width=600 align=center>
			</td>
			</tr>
			<tr>
				<td align=center>
<?php		
			$upurl = $upurl.idalf(12,10)."&js_on=1&xpass=".idalf(22)."&xmode=1";
	
            $url=parse_url($upurl);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$referrer, $cookie, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	

			is_notpresent($upfiles,"file_status[]'>OK","Error upload file",0);
			$act=cut_str($upfiles,"action='","'");
			$dat=cut_str($upfiles,"POST'><textarea name=","/textarea></Form>");
			preg_match_all('/\'.+?</',$dat,$datas);
			$post=array();
			foreach ($datas[0] as $data){
			$tmp=str_replace(">","=",$data);
			$quer=str_replace("<","",$tmp);
            $tquer=explode("'",$quer);
			$post[$tquer[1]] = str_replace("=","",$tquer[2]);
			}	
			$Url=parse_url($act);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, $post, 0, $_GET["proxy"],$pauth);
			is_page($page);
			
			preg_match('/location: *(.*)/i', $page, $redir);
			$Href = rtrim($redir[1]);
			$Url = parse_url($Href);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
            preg_match_all('/http:\/\/up-file\.com\/download\/[\/\w.]+/i', $page, $dwn);
			$download_link=$dwn[0][0];
			$delete_link=$dwn[0][1];
			}
			
			function idalf ($len,$typ=24){
			$vect = array("0", "1", "2", "3", "4", "5", "6", "7", "8","9","a","b","c","d","e","f","g","A","B","C","D","E","F","G");
			for ($i = 1; $i <= $len; $i++) {
			 $pos=rand(0, $typ);
             $id .= $vect[$pos];
			}
			return $id;
			}
/*************************\
 WRITTEN BY KAOX 01-jun-09
 UPDATE BY KAOX 04-oct-09
\*************************/
	
?>