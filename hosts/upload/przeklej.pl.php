<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$url = "http://www.przeklej.pl";
			$Url = parse_url($url);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode(';',$cookie);
			
			$token=cut_str($page,'hidden" name="token" value="','"');
			
			$post["Filename"] = $lname;
			$post['token'] = $token;
			$post["newUpload"] = "1";
			$post['Upload'] = "Submit Query";
						
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
			$url = parse_url('http://www.przeklej.pl/dodaj_plik');
			$upagent = "Shockwave Flash";
			$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, $cookies, $post, $lfile, $lname, "plik[plik]",0,$upagent);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
			
			unset ($cookies);
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
			$cookie = $temp[1];
			$cookies = implode(';',$cookie);
			
			$fileidsup = cut_str($upfiles,'f,',',');
			$filetokens = "tok%3A".cut_str($upfiles,'tok:','\n');
			
			$upost['fileids[fileids]'] = $fileidsup;
			$upost['filetokens'] = $filetokens;
			
			$page = geturl("www.przeklej.pl", 80, "/ndodaj", "http://www.przeklej.pl/", $cookies, $upost, 0, "");
			is_page($page);

			$fileids = cut_str($page,'name="file[fileids]" value="','"');
			
			$epost['filetokens'] = $filetokens;
			$epost['fileids%5Bfileids%5D'] = $fileids;
			$epost['file%5Bdescription_'.$fileids.'%5D'] = "";
			$epost['privacy-status'] = 1;
			$epost['file%5Bhaslo_pliki%5D'] = "";
			$epost['email'] = 0;
			$epost['file%5Bemail%5D'] = "";
			$epost['directory-val'] = 0;
			$epost['file%5Bfoldername%5D'] = "";
			$epost['file%5Bdescription%5D'] = "";
			$epost['file%5Bhaslo%5D'] = "";
			
			$page = geturl("www.przeklej.pl", 80, "/dodaj_pliki", "http://www.przeklej.pl/", $cookies, $epost, 0, "");
			is_page($page);
			$name = strtr($lname , '.', '-');
			$download_link = 'http://www.przeklej.pl/plik/'.$name.'-'.$fileidsup;
// Made by Baking 16/10/2009 06:46
?>