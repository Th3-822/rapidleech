<table width=600 align=center>
</td></tr>
<tr><td align=center>
    
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
//************************ Login *******************
$sendspace_login="";
$sendspace_pass="";
//**************************************************

$not_done = true;
$continue_up = false;

if ($sendspace_login & $sendspace_pass) {
	$_REQUEST ['my_login'] = $sendspace_login;
	$_REQUEST ['my_pass'] = $sendspace_pass;
	$_REQUEST ['action'] = "FORM";
	echo "<b><center>Use Default Sendspace.com login/pass.</center></b>\n";
}

if ($_REQUEST ['action'] == "FORM")
	$continue_up = true; else {
	?>
<table border=1 style="width: 540px;" cellspacing=0 align=center>
	<form method=post><input type=hidden name=action value='FORM' />
	
	<tr >
	  <td colspan=4 align=center height=25px ><b>	Enter Free or Premium Account</b> </td>
	</tr>
	<tr>
		<td nowrap>&nbsp;Login		
		<td>&nbsp;<input name=my_login value='' style="width: 160px;" />&nbsp;		
		<td nowrap>&nbsp;Password		
		<td>&nbsp;<input type=password name=my_pass value='' style="width: 160px;" />&nbsp;	
	</tr>	
	<tr>
		<td nowrap colspan=0>&nbsp;Note		
		<td colspan=3>&nbsp;<b>If you have no sendspace account then, kindly press upload button to upload</b>&nbsp;			
	</tr>
	<tr>
		<td colspan=4 align=center><input type=submit value='Upload' />
	</tr>	
</table>
</form>
<?php
}

if ($continue_up) {

            $page = geturl("www.sendspace.com", 80, "/", "", 0, 0, 0, "");
?>
    <script>document.getElementById('info').style.display='none';</script>
<?php 
function biscotti($content) {
        is_page($content);
        preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
        foreach ($matches[0] as $coll) {
        $bis.=cut_str($coll,"Set-Cookie: ","; ")."; ";	
        }return $bis;}
        
			$sendspace_login = $_REQUEST ['my_login'];
			$sendspace_pass = $_REQUEST ['my_pass'];
			
            $cook=biscotti($page);
     		if ($sendspace_login && $sendspace_pass){
			$post["action"]="login";
			$post["username"]=$sendspace_login;
			$post["password"]=$sendspace_pass;
            $post["remember"]="1";
			$post["submit"]="login";
			$post["openid_url"]="";
			$post["action_type"]="login";
			$page=geturl("www.sendspace.com", 80, "/login.html", "http://www.sendspace.com/login.html", $cook, $post, 0, $_GET["proxy"]);
			$cook=$cook." ".biscotti($page);
			is_present($cook,"ssal=deleted","Login incorrect retype your username or password correctly");
			$page=geturl("www.sendspace.com", 80, "/", "http://www.sendspace.com/", $cook, 0, 0, $_GET["proxy"]);
			unset($post);
			}else{
				echo("<br> <b>No enter login & pass</b> to sendspace.com <br><br>");
			}

            $tmp = cut_str($page,'DESTINATION','>');            
            $DESTINATION_DIR=cut_str($tmp,'value="','"');
            
            $url_action=cut_str($page,'post" action="','"');
            $UPLOAD_IDENTIFIER=cut_str($page,'name=UPLOAD_IDENTIFIER value="','"');
            $UPLOAD_IDENTIFIER=$UPLOAD_IDENTIFIER ? $UPLOAD_IDENTIFIER : cut_str($page,'name="UPLOAD_IDENTIFIER" value="','"');
			$signature=cut_str($page,'signature" value="','"');
            
            if (empty($url_action) || empty($UPLOAD_IDENTIFIER) || empty($DESTINATION_DIR))
                {    
                    html_error("Error retrive upload id".$page);
                }
            
			$post["MAX_FILE_SIZE"]="314572800";
			$post["UPLOAD_IDENTIFIER"]=$UPLOAD_IDENTIFIER;
            $post["DESTINATION_DIR"]=$DESTINATION_DIR;
            $post["js_enabled"]="1";
			$post["signature"]=$signature;
//            $post["terms"]="1";
			$post["recpemail"]="";
            if ($sendspace_login && $sendspace_pass) $post["userid"]=cut_str($page,'userid" value="','"');
            $post["desc0"]=$descript;
//			$post["btnupload"]="Upload File";
            
            $url=parse_url($url_action);
            $upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://www.sendspace.com/", $cook, $post, $lfile, $lname, "file_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php        
			is_page($upfiles);
			is_present($upfiles,"uploadprocerr.html","Error Upload file! / Ошибка загрузки файла!");

            //$page = geturl($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$url_action, $cook, 0, 0, "");
            //is_page($page);
            $tmp = cut_str($upfiles,'Download Link in HTML',"'>");
            $download_link=cut_str($tmp,"href='",'"');
            $tmp = cut_str($upfiles,'File Delete Link','/>');
            $delete_link=cut_str($tmp,'value="','"');

}
// Fixed by kaox 07/05/09
// Added GUI for asking username and password by Raj Malhotra
// Fixed "Error retrive upload idHTTP/1.1 301 " by Raj Malhotra
?>