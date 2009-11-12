<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$url = "http://www.sharehoster.de";
			$Url = parse_url($url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			
			$uid = $i=0; while($i<32){ $i++;}
			$uid += floor(rand() * 16)."16";
			
			$upfrom= "upload01.sharehoster.de";
			
			$post["Upload"]="Submit Query";						
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url('http://'.$upfrom.'/upload.php?redirect=yes&X-Progress-ID='.$uid);
			$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 'http://'.$upfrom.'/', 0, $post, $lfile, $lname, "userfile");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode(';',$cookie);
			
			$locat=trim(cut_str($upfiles,'Location:',"\n"));
			
			$Url = parse_url($locat);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://www.sharehoster.de/', $cookies, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			
					
			$download_link = 'http://www.sharehoster.de/dl/'.cut_str($page,"filelist[0]['id'] = '", "'");
			$delete_link= 'http://www.sharehoster.de/del/'.cut_str($page,"filelist[0]['id'] = '", "'")."/".cut_str($page,"filelist[0]['ident'] = '", "'");
// Made by Baking 16/10/2009 07:48
?>