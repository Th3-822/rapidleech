<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php 

$hotfile_username="";  // username
$hotfile_password="";  // password

if ($hotfile_username && $hotfile_password){
  $in=parse_url("http://hotfile.com/login.php");
$post=array();
$post["returnto"]="/";
$post["user"]=$hotfile_username;
$post["pass"]=$hotfile_password;
$page = geturl($in["host"], $in["port"] ? $in["port"] : 80, $in["path"].($in["query"] ? "?".$in["query"] : ""), "http://hotfile.com/", 0, $post, 0, $_GET["proxy"],$pauth);	
preg_match('/auth=\w{64}/i', $page, $ook);
$cook=$ook[0];
if(!$cook){
html_error("Login Failed , Bad username/password combination.",0);
}
}

$hserver=parse_url("http://hotfile.com/");
$page=geturl($hserver['host'],"80","http://hotfile.com/");

preg_match_all('/(action)[ ="]+.+?"/', $page, $act);
$action = preg_replace('/(action)[ ="]+/i', '', $act[0][1]);
$action = str_replace("\"","",$action);
 
 $url=parse_url($action);
      $post['submit']="Upload";
	  $upfiles=upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://hotfile.com/", $cook, $fpost, $lfile, $lname, "uploads[]");
	  
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

