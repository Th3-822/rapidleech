<?php 

$sprezer_username="";  // username
$sprezer_password="";  // password

            $referrer="http://www.sprezer.com/upload";
            $Url = parse_url("http://www.sprezer.com/login");
			
			$fpost['sUsername'] = $sprezer_username;
			$fpost['sPassword'] = $sprezer_password;
			$fpost['login_submit'] = "Login";
			
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, 0, $fpost, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
            $cook = $temp[1];
            $cookie = implode(';',$cook);
			$Url = parse_url($referrer);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);

			$quer=cut_str($page, 'UPLOAD_IDENTIFIER" value="', '"');
			unset($fpost);
			$fpost['UPLOAD_IDENTIFIER'] = $quer;
			$fpost['upload_submit'] = "";
			$fpost['hoster_rapidshare'] = "on";
			$fpost['hoster_netload'] = "on";
			$fpost['hoster_uploaded'] = "on";
			$fpost['hoster_load'] = "on";
			
	?>
<script>document.getElementById('info').style.display='none';</script>

		<table width=600 align=center>
			</td>
			</tr>
			<tr>
				<td align=center>
<?php		
					
			$url = parse_url($referrer);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$referrer, $cookie, $fpost, $lfile, $lname, "file");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	

			is_notpresent($upfiles,"Upload erfolgreich","Error upload file",0);
			preg_match('/http:\/\/www\.sprezer\.com\/file[^\'"]+/', $upfiles, $down);
			preg_match('/http:\/\/www\.sprezer\.com\/kill[^\'"]+/', $upfiles, $del);
			$download_link=$down[0];
			$delete_link = $del['0'];
			
// written by kaox 31/05/2009
?>