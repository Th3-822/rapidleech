<?php
####### Account Info. ###########
$novamov_login = "";
$novamov_pass = "";
$novaup_cat = "3"; // 0 = Unknown; 1 = Software; 3 = Documents; 4 = Videos; 6 Games
##############################

$not_done=true;
$continue_up=false;
if ($novamov_login & $novamov_pass){
	$_REQUEST['bin_login'] = $novamov_login;
	$_REQUEST['bin_pass'] = $novamov_pass;
	$_REQUEST['cat'] = $novaup_cat;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Let it empty for free user</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=1 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM'><input type=hidden value=uploaded value'<?php $_REQUEST[uploaded]?>'>
<input type=hidden name=filename value='<?php echo base64_encode($_REQUEST[filename]); ?>'>
<tr><td nowrap>&nbsp;Login<td>&nbsp;<input name=bin_login value='' style="width:160px;">&nbsp;</tr>
<tr><td nowrap>&nbsp;Password<td>&nbsp;<input name=bin_pass value='' style="width:160px;">&nbsp;</tr>
<tr><td nowrap>&nbsp;Categories<td>&nbsp;<select name="cat" id="cat">
  <option value="0">Unknown</option>
  <option value="1">Software</option>
  <option value="3">Documents</option>
  <option value="4">Videos</option>
  <option value="6">Games</option>
</select></tr>
<tr><td colspan=2 align=center>Let it empty for free user</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload'></tr>
</table>
</form>
<?
}

if ($continue_up)
	{
		$not_done=false;
?>
<table width=600 align=center> 
</td></tr> 
<tr><td align=center> 
<div id=login width=100% align=center></div> 
<?php
			$Url=parse_url('http://www.novamov.com/login.php?return=');
			if ($_REQUEST['action'] == "FORM")
			{
				$post["user"]=$_REQUEST['bin_login'];
				$post["pass"]=$_REQUEST['bin_pass'];
				$post["Login.x"]=rand(1,99);
				$post["Login.y"]=rand(1,99);
			
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.novamov.com/login.php", 0, $post, 0, $_GET["proxy"], $pauth);
			$cookies=GetCookies($page);
			
			}	
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$Url=parse_url('http://www.novamov.com/panel.php?q=3');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, $cookies, 0, 0, $_GET["proxy"], $pauth);
			$uquery = cut_str($page,'<iframe src="','"');
			$userid = cut_str($uquery,'u=','&key');
			$cookie = "user=".$userid."";
			$ref='http://k.novaup.com:8080/upload/ubr_file_'.$uquery.'';
			$Url2 = 'http://k.novaup.com:8080/upload/ubr_link_upload.php?rnd_id='.time().'';
			$Url=parse_url($Url2);
			$page = geturl($Url["host"], 8080, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $ref, $cookie, 0, 0, $_GET["proxy"], $pauth);
			
			$upload_id = cut_str($page,'startUpload("','"');
			$post["title"]= $lname;
			$post["desc"]= "Description";
			$post["ui"]= $userid;
			$post["cat"]= $_REQUEST['cat'];	
			$url2 ='http://k.novaup.com:8080/cgi-bin/ubr_upload.pl?upload_id='.$upload_id.'';
			$url = parse_url($url2);		
			$upfiles=upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), $ref, 0, $post, $lfile, $lname, "upfile_0");
			$ufinished = cut_str($upfiles,'<p>The document has moved <a href="','"');
			$Url=parse_url($ufinished);
			$page = geturl($Url["host"], 8080, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $ref, $cookie, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 
			preg_match('#(http:\/\/www\.novaup\.com\/download\/([a-z0-9]+))\&d=(http:\/\/www\.novaup\.com\/delete\/([a-z0-9]+)\/([a-z0-9]+))#', $page, $temp);
			$download_link= $temp[1];
			$delete_link= $temp[3];
}       
?>