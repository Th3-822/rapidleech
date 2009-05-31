<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://dl.free.fr/';
			$page = geturl("dl.free.fr", 80, "/", "", 0, 0, 0, "");
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php
			is_page($page);
			
			$url_action=cut_str($page,'/upload.pl?','"');
			if (!$url_action)
				{	
					html_error("Error retrive upload id".$page);
				}
			
			$post["mail1"]='';
			$post["mail2"]='';
			$post["mail3"]='';
			$post["mail4"]='';
			$post["message"]=$descript;
			$post["password"]='';

			$url=parse_url($ref.'upload.pl?'.$url_action);

			$upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, 0, $post, $lfile, $lname, "ufile");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		
			is_page($upfiles);
			$locat=trim(cut_str($upfiles,'Location:',"\n"));
			if (!$locat) {html_error("Error get location".$upfiles);}
			$Url=parse_url($locat);
			echo "Pass: ";
			for ($i=1;$i<10;$i++)
				{
				sleep(3);
				$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref, 0, 0, 0, $_GET["proxy"],$pauth);
				is_page($page);
				echo "$i ";
				if (strpos($page,"proc&eacute;dure termin&eacute;e avec succ&egrave;s")){break;}
				}

			$tmp=cut_str($page,'adresse suivante',true);
			
			$download_link=cut_str($tmp,'<a href="','"');
			$tmpd=cut_str($tmp,'http://dl.free.fr/rm.pl?','"');
			if ($tmpd){
				$tmpd='http://dl.free.fr/rm.pl?'.$tmpd;
				$tmpd=str_replace("&amp;","&",$tmpd);
			}
			$delete_link=$tmpd;
?>
<script>document.getElementById('final').style.display='none';</script>
<?php


// sert 30.07.2008
?>