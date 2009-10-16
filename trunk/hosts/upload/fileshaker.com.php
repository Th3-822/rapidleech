<script>document.getElementById('login').style.display='none';</script>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php
	
	$url=parse_url('http://www.fileshaker.com/index.php?xml=true');
	
	$post['Filename']= $lname;
	$post['Upload']= 'Submit Query';
	
	$upagent = "Shockwave Flash";
	$upfiles=upfile($url["host"], 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $post, $lfile, $lname, "file",0,$upagent);
	is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	$locat=cut_str($upfiles,'shorturl=',true);
	$Url=parse_url("http://www.fileshaker.com/".$locat);
	$page = geturl($Url["host"],  80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://www.fileshaker.com/', 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	
	$download_link= 'http://www.fileshaker.com/'.$locat;
	
	
// Made by Baking 30/07/2009 20:33

?>