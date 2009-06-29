<?php
//********* Login ************
$_POST["login"]="";// username
$_POST["pwd"]="";  // password
//****************************
if(!$_POST["login"] || !$_POST["pwd"])
{
	$not_done=true;
?>
      <table border=1 style="width: 540px;" cellspacing=0 align=center>
    <form method=post> 
    
    <tr >
      <td colspan=4 align=center height=25px ><b>Enter Premium Account</b> </td>
    </tr>
    <tr>
        <td nowrap>&nbsp;Login        
        <td>&nbsp;<input name=login value='' style="width: 160px;" />&nbsp;        
        <td nowrap>&nbsp;Password        
        <td>&nbsp;<input type=password name=pwd value='' style="width: 160px;" />&nbsp;    
    </tr>    

    <tr><td colspan=4 align=center><input type=submit value='Upload' /></tr>    
</table>
</form>
    
<?php
}
else
{

	echo "<div id=login width=100% align=center>Login to uploading.com</div>";
   
    $post=array();
    $post["log_ref"]="";
    $post["login"]=$_POST["login"]; 
    $post["pwd"]=$_POST["pwd"];
     
	$page = geturl("www.uploading.com", 80, "/login/", 0, 0, $post, 0 );
	$cookies=BiscottiDiKaox($page);
	$page = geturl("www.uploading.com", 80, "/", "http://www.uploading.com/login", $cookies);
	is_notpresent($page, "logout", "Login failed<br>Wrong login/password?");
	
	echo
<<<HTML
	<script>document.getElementById('login').style.display='none';</script>
	<table width=600 align=center>
	</td></tr>
	<tr><td align=center>
HTML;

	preg_match('#http://[^.]+\.uploading.com/upload.php\?X-Progress-ID=#', $page, $preg) or html_error("Upload url not found");
	$id = "";
	for($i=0; $i<32; $i++)
	{
		$id .= base_convert(floor(rand(0, 15)), 10, 16);
	}
	
	$url = parse_url($preg[0] . $id);
	
	$post = array
		(
			u_id    => cut_str($page, "name='u_id' type=hidden value='", "'"),
			message => "",
			pass    => "",
			toemail => ""
		);

	$page = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://www.uploading.com/", $cookies, $post, $lfile, $lname, "userfile");
	
	echo
<<<HTML
	<script>document.getElementById('progressblock').style.display='none';</script>
HTML;

	$page = geturl("www.uploading.com", 80, "/uploadinfo.php?GET=$id", "http://www.uploading.com/", $cookies);
	is_page($page);

	preg_match('#http://uploading.com/files/[^\'"<]+#', $page, $preg) or html_error("Upload error");
	$download_link = $preg[0];
	echo "<h3><font color='green'>File successfully uploaded to your account</font></h3>";	
}
function BiscottiDiKaox($content)
 {
 preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
 foreach ($matches[1] as $coll) {
 $bis0=split(";",$coll);
 $bis1=$bis0[0]."; ";
 $bis2=split("=",$bis1);
if  (substr_count($bis,$bis2[0])>0)
{$patrn=$bis2[0]."[^ ]+"; 
$bis=preg_replace("/$patrn/",$bis1,$bis);     
} else{$bis.=$bis1 ; }}
$bis=str_replace("  "," ",$bis);     
return rtrim($bis);}

//written by kaox 07/05/09

?>
