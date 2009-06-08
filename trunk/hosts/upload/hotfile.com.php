<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php 

$hserver=parse_url("http://hotfile.com/");
$page=geturl($hserver['host'],"80","http://hotfile.com/");

preg_match_all('/(action)[ ="]+.+?"/', $page, $act);
$action = preg_replace('/(action)[ ="]+/i', '', $act[0][1]);
$action = str_replace("\"","",$action);
 
 $url=parse_url($action);
      $post['submit']="Upload";
	  $upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://hotfile.com/", 0, $fpost, $lfile, $lname, "uploads[]");
	  
 if(preg_match('/Location: *(.+)/', $upfiles, $redir)){
 $redirect=rtrim($redir["1"]);
 $Url = parse_url($redirect); 
 }else{html_error("Error, no download link retrieved","0");}        
 $page=geturl($Url['host'],"80",$Url ["path"]);
 
  preg_match('/http:\/\/.+dl[^\'"]+/', $page, $dwn);  
  preg_match('/http:\/\/.+kill[^\'"]+/', $page, $del);
  $download_link = rtrim($dwn['0']);
  $delete_link = rtrim($del['0']);
      
?>

