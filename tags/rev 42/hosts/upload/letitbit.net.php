<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$ref='http://www.letitbit.net/';
			$Url = parse_url($ref);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
//			echo $page;
			$uid = $i=0; while($i<12){ $i++;}
			$uid += floor(rand() * 10);
			
			$upfrom=cut_str($page,'form-data" action="','"').$uid;
			$refup="http://".cut_str($upfrom,'http://','/cgi-bin');
			$port=cut_str($upfrom,'letitbit.net:','/cgi-bin');
			
			$xmode=cut_str($page,'xmode" value="','"');
			$pbmode=cut_str($page,'pbmode" value="','"');
			$base=cut_str($page,'base" type="hidden" id="owner" value="','"');
			$host=cut_str($page,'host" type="hidden" id="owner" value="','"');
			$tmpl=cut_str($page,'tmpl_name" value="','"');
			
?>
<script>document.getElementById('info').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

			$post["xmode"]="$xmode";	$post["pbmode"]="$pbmode";
			$post["owner"]="";			$post["pin"]= "";
			$post["base"]="$base";		$post["host"]="$host";
			$post["css_name"]="";		$post["tmpl_name"]= "pro1";
			$url = parse_url($upfrom);
			$upfiles = upfile($url["host"], $port, $url["path"].($url["query"] ? "?".$url["query"] : ""), $refup, 0, $post, $lfile, $lname, "file_0");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<div id=final width=100% align=center>Get final code</div>
<?php		

			is_page($upfiles);
			$up2 = cut_str($upfiles,"<Form name='F1' action='" ,"'") ;
			$ref2= cut_str($upfiles,"action='" ,"/uploadtwo") ;
	$gpost['file_name_orig[]'] = cut_str($upfiles,'file_name_orig[]\'>' ,'</textarea>') ;
	$gpost['file_status[]'] = cut_str($upfiles,'file_status[]\'>' ,'</textarea>') ;
	$gpost['file_size[]'] = cut_str($upfiles,'file_size[]\'>' ,'</textarea>') ;
	$gpost['file_descr[]'] = cut_str($upfiles,'file_descr[]\'>' ,'</textarea>') ;
	$gpost['file_mime[]'] = cut_str($upfiles,'file_mime[]\'>' ,'</textarea>') ;
	$gpost['number_of_files'] = "1" ;
	$gpost['ip'] = cut_str($upfiles,'ip\'>' ,'</textarea>') ;
	$gpost['host'] = cut_str($upfiles,'host\'>' ,'</textarea>') ;
	$gpost['duration'] = cut_str($upfiles,'duration\'>' ,'</textarea>') ;
	$gpost['target_dir'] = cut_str($upfiles,'target_dir\'>' ,'</textarea>') ;
	$gpost['owner'] = '';
	$gpost['pin'] = '';
	$gpost['base'] = cut_str($upfiles,'base\'>' ,'</textarea>') ;
	$gpost['host'] = 'letitbit.net';
			$Url = parse_url($up2);
			$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref2, 0, $gpost, 0, $_GET["proxy"],$pauth);
//	echo $page;		
			$ddl=cut_str($page,'</h1>  <textarea cols=65 rows=15>',"</textarea>");
			$del=cut_str($page,'</h3>  <textarea cols=65 rows=15>',"</textarea>");
			
			$download_link=$ddl;
			$delete_link= $del;


// Made by Baking 13/09/2009 21:51
?>