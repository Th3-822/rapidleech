<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
				$ref='http://www.badongo.com/';
			if ($badongo_login && $badongo_pass){
				$mem="member";
				$page = geturl("www.badongo.com", 80, "/login", "", 0, 0, 0, "");
				$post["username"]=$badongo_login;
				$post["password"]=$badongo_pass;
				$post["cap_id"]="";
				$post["cap_secret"]=cut_str($page,'cap_secret" value="','"');
				$post["bk"]="";
				$post["do"]="login";
				$post["cou"]="en";
				$page = geturl("www.badongo.com", 80, "/", "http://www.badongo.com/login", 0, $post, 0, "");
				is_page($page);
				if (!strpos($page,$badongo_login)){$mem="";echo"<br><b>Error Login. Use no member upload</b><br>";}else
					{echo"<br><b>Login OK. Use member upload</b><br>";}
				$cookies=GetCookies($page);
			}
//            $page = geturl("badongo.com", 80, "/", "", 0, 0, 0, "");
//            is_page($page);
//            if (preg_match('/upload\\.badongo\\.com\/[a-z0-9\\_]*\/.\//', $page, $regs)) {
//                $url = parse_url("http://".$regs[0]);
//                print_r($regs);
//            }else html_error("Error retrive action page!");
//http://upload.badongo.com/upload_single/f/?cou=en&k=member
			$url=parse_url("http://upload.badongo.com/upload_single/f/?cou=en&k=".$mem);
            $page = geturl($url['host'], 80, $url['path']."?".$url["query"], $ref, $cookies, 0, 0, "");
			is_page($page);
?>
    <script>document.getElementById('info').style.display='none';</script>
<?php
			unset($post);
            $post["UPLOAD_IDENTIFIER"]=cut_str($page,'UPLOAD_IDENTIFIER" value="','"');            
            $post["affiliate"]="";
			$post["desc"]=$descript;
			$post["sub"]="Upload";
			$post["toc"]=1;
			$tmp=cut_str($page,'<FORM','>');
			$action_url=cut_str($tmp,'action="','"');
			$url=parse_url($action_url);
            
            $upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, $post, $lfile, $lname, "filename");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
            
            $locat = cut_str($upfiles,"location.href='","'");
            $temp1=cut_str($upfiles,"'&s=","'");
            
            $url = parse_url($locat.$temp1);
            $page = geturl($url['host'],80,$url['path']."?".$url["query"],$ref);
            
            is_page($page);
//			if(empty($tmp))
			$tmp = trim(cut_str($page,"defaultContent: '","'"));
			if (!$tmp) {
				$tmp=cut_str($page,'downBtn','</a>');
				$tmp=cut_str($tmp,'a href="','"');
			}
			if (!$tmp) html_error("Error get download link<br>".$page);
			$download_link=$tmp;
			$tmp=trim(cut_str($page,'http://www.badongo.com/delete/',"'"));
            if ($tmp) $delete_link="http://www.badongo.com/delete/".$tmp;

// Edited by sert 20.06.2008
?>