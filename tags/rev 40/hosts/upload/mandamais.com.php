<?php 
            $referrer="http://www.mandamais.com.br/";
            $Url = parse_url($referrer."scripts_upload/upload_file.js");  
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
            $cook = $temp[1];
            $cookie = implode(';',$cook);	
			$tid=time() % 1000000000;
			$quer=cut_str($page, 'MyForm.action = "', '"');
			$url_action = "http://".$Url["host"].$quer.$tid;
			$fpost['descricao'] = $lname;
			$fpost['categoria'] = "6";
			$fpost['tipo_p'] = "1";
			
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
			// written by kaox 25/05/2009
	
?>