<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref="http://www.rapidshare.ru/";
			if (isset($rapidshareru_login) && isset($rapidshareru_pass))
			{
				$post['page']='page=www.rapidshare.ru%2F';
				$post['userEmail']=str_replace("@","%40",$rapidshareru_login);
				$post['userPass']=$rapidshareru_pass;
				$url=parse_url($ref."login.php");
				$page = geturl($url["host"],defport($url),$url['path'].($url["query"] ? "?".$url["query"] : ""),$ref,0,$post,0,0,0);
				is_page($page);
				$cookies = GetCookies($page);
				if (strpos($page,"userID"))
				{echo"<br><b>Login OK. Use member upload</b><br>";}
				else{
					echo"<br><b>Error Login. Use no member upload</b><br>";
					}
			}
            $page = geturl("www.rapidshare.ru", 80, "/", "", 0, 0, 0, "");
			is_page($page);
			$tmp = cut_str($page,'id=actionurl','/>');
			$action_url = cut_str($tmp,'value="','"');
			if (!$action_url) html_error("Error retrive upload id <br>".$page);

			$url=parse_url($action_url);

?>
<script>document.getElementById('info').style.display='none';</script>
<?php

			@$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookies, 0, $lfile, $lname, "fl1");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php		
			is_page($upfiles);
//			is_notpresent($upfiles,"ok","Error Upload File!".$upfiles);
            $location = trim(cut_str($upfiles,"Location: ","\n"));
			if (!$location) html_error("Error upload file <br>".$page);
            $url=parse_url($location);
            $page = geturl($url["host"],defport($url),$url['path'].($url["query"] ? "?".$url["query"] : ""),$ref,$cookies,0,0,0,0);
            is_page($page);
            is_notpresent($page,"$lname","Error Upload file <br>".$page);
            
            $download_link = cut_str($page,'[URL]','[/URL]');

// sert 22.06.2008
?>