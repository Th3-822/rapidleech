<table width=600 align=center>
</td></tr>
<tr><td align=center>
    
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
//************************ Login *******************
$sendspace_login="";
$sendspace_pass="";
//**************************************************

            $page = geturl("sendspace.com", 80, "/", "", 0, 0, 0, "");
?>
    <script>document.getElementById('info').style.display='none';</script>
<?php 
function biscotti($content) {
        is_page($content);
        preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
        foreach ($matches[0] as $coll) {
        $bis.=cut_str($coll,"Set-Cookie: ","; ")."; ";	
        }return $bis;}
        
            $cook=biscotti($page);
     		if ($sendspace_login && $sendspace_pass){
			$post["action"]="login";
			$post["username"]=$sendspace_login;
			$post["password"]=$sendspace_pass;
            $post["remember"]="1";
			$post["submit"]="login";
			$post["openid_url"]="";
			$post["action_type"]="login";
			$page=geturl("sendspace.com", 80, "/login.html", "http://sendspace.com/login.html", $cook, $post, 0, $_GET["proxy"]);
			$cook=$cook." ".biscotti($page);
			is_present($cook,"ssal=deleted","Login incorrect retype your username or password correctly");
			$page=geturl("sendspace.com", 80, "/", "http://sendspace.com/", $cook, 0, 0, $_GET["proxy"]);
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
            $upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://sendspace.com/", $cook, $post, $lfile, $lname, "file_0");
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

// Fixed by kaox 07/05/09
?>