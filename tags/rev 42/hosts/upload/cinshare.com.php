<?
// Baking addon !
function GRC($length = 32, $letters = 'abcdef1234567890')
  {
      $s = '';
      $lettersLength = strlen($letters)-1;
     
      for($i = 0 ; $i < $length ; $i++)
      {
      $s .= $letters[rand(0,$lettersLength)];
      }
     
      return $s;
  } 
// End addon
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php

	
	$ref='http://www.cinshare.com/home';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://www.cinshare.com/', 0, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$rndid = cut_str($page,'var fid = genRandId(',')');
	$uid = cut_str($page,'<input id="uid" type="hidden" value="','"');
	$ID = GRC();
	$servup = cut_str($page,"var curServer = '","'");
	$post['Filename']= $lname;
	$post['Upload']= 'Submit Query';
	$url=parse_url($servup."/upload/process/".$ID."/".$uid);
	$upagent = "Shockwave Flash";
	$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, 0, $post, $lfile, $lname, "Filedata",0,$upagent);
	is_page($upfiles);
	
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	$Url=parse_url('http://www.cinshare.com/upload/getLinks/'.$ID);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://www.cinshare.com/', 0, 0, 0, $_GET["proxy"],$pauth);
	$ddl=cut_str($page,'value="http://www.cinshare.com/files/get/','"');
	$download_link= "http://www.cinshare.com/files/get/".$ddl;
	$delete_link= "http://www.cinshare.com/files/delete/".$ID;
	
// Made by Baking 30/07/2009 16:59
?>