<?
####### Free Account Info. ###########
$md_login = "";
$md_pass = "";
##############################

$not_done=true;
$continue_up=false;
if ($md_login & $md_pass){
	$_REQUEST['login'] = $md_login;
	$_REQUEST['password'] = $md_pass;
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;Login*<td>&nbsp;<input type=text name=login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=password value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap><label for="name">&nbsp;Transload* 
<td>&nbsp;Video<input type="radio" name="AV" value="video" onClick="javascript:var displ=this.checked?'':'none';document.getElementById('videoblock').style.display=displ;">
&nbsp; File<input type="radio" name="AV" value="file">  
</table>
<table border=0 id="videoblock" style="display: none;" cellspacing=0 align=center>
<tr><td nowrap>&nbsp;</td></tr>
<tr><td nowrap><td><small style="color:#F00000;">Video Options :</small></tr>
<tr><td nowrap>&nbsp;</td></tr>
<tr><td nowrap>&nbsp;Title*<td>&nbsp;<input name="title" style="width:160px;" maxlength="60">&nbsp;</tr>
<tr><td nowrap>&nbsp;Description*<td>&nbsp;<textarea name="description" style="width:160px;"></textarea>&nbsp;</tr>
<tr><td nowrap>&nbsp;Tags*<td>&nbsp;<input name="tags" style="width:160px;" maxlength="120">&nbsp;</tr>
<tr><td nowrap><td><small>&nbsp;Enter each tag seperated by a <br>&nbsp;comma,for example tag1,tag2 etc...</small></tr>
<tr><td nowrap>&nbsp;Category*<td>&nbsp;<select name="category" id="vid-cat">
<option value="-1"> - </option> 
<option value="0">Animation</option> 
<option value="1">Anime</option> 
<option value="2">Automobiles & Cars</option> 
<option value="3">Celebrities & Showbiz</option> 
<option value="4">Educational</option> 
<option value="5">Entertaiment +</option> 
<option value="6">Faith & Lifestyle</option> 
<option value="7">Games</option> 
<option value="8">Movies</option> 
<option value="9">Living</option> 
<option value="10">Music</option> 
<option value="11">News & Politics</option> 
<option value="12">Science & Technology</option> 
<option value="13">Sports</option> 
<option value="14">Travel & Tourism</option> 
<option value="15">Tutorials</option> 
<option value="16">TV Shows</option> 
<option value="17">Wrestling</option> 
<option value="18">Web Series</option> 
<option value="19">Miscellaneous</option> 
</select></tr>
<tr><td nowrap><label for="name">&nbsp;Filter* 
<td><input type="radio" name="vid-ex" value="0" checked="1"><small>Suitable for all audiences</tr>
<td><td><input type="radio" name="vid-ex" value="1"><small>Suitable for adult (18+) audiences<br><br></small>
<tr><td nowrap><label for="name">&nbsp;Privacy Level* 
<td><input type="radio" name="privacy" value="0" checked="1"><small>Open (Open to all)</tr></small>
<td><td><input type="radio" name="privacy" value="1"><small>Secret (Not Publicly Visible)</small></tr>
<tr><td colspan=2 align=center><small style="color:#FFEE00">* Required Field or upload will fail !</small></tr>
</table>
<br>
<center><input type=submit value='Upload' /><center>
</form>
<?php
}

if ($continue_up)
	{
		$not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=login width=100% align=center>Login to milledrive.com</div>
<?php
			$post['username'] = $_REQUEST['login'];
			$post['password'] = $_REQUEST['password'];
			$post['next'] = '%2F' ;
			$page = geturl("milledrive.com", 80, "/login/", 0, 0, $post, 0, $_GET["proxy"], $pauth);
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 302 FOUND', 'Error logging in - are your logins correct? First');
					
			$cok1 = "__qca=49c6748d-2bbb5-33b9e-d5e5a; ";
			$cok2 = "__qcb=".(time()+(6*3600));
			$cok3 = "sessionid=".cut_str($page,'sessionid=',' ')."; ";
			$cookies = "$cok1$cok3$cok2";
			
			$page = geturl("milledrive.com", 80, "/upload/", 0, $cookies, 0, 0, "");
			is_page($page);
			is_notpresent($page, 'HTTP/1.1 200 OK', 'Error logging in - are your logins correct?Second');
?>
<script>document.getElementById('login').style.display='none';</script>
<?php
if($_POST['AV'] != "video" && $_POST['AV'] != "file")
        { html_error('Choise a methode to Transload ! Video or File ');}
if($_POST['AV'] == "video")
  {

?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID (Video)</div>
<?php

	$ref='http://milledrive.com/upload/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://milledrive.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	
	is_page($page);
	$upfrm = cut_str($page,'name="vid-upl-frm" enctype="multipart/form-data" action="','"');
	$servup = cut_str($page,'name="vid-upl-frm" enctype="multipart/form-data" action="http://','.milledrive.com');
	$refup = 'http://mds04.milledrive.com/';
	
	$ref='http://milledrive.com/uuid?churl=V';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://milledrive.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_notpresent($page, 'HTTP/1.1 301 MOVED PERMANENTLY', 'Error getting upload url');
	
	
	$t1 = 'http://milledrive.com/uuid/?churl=V';
	$Url=parse_url($t1);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://milledrive.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_notpresent($page, 'HTTP/1.1 200 OK', 'Error getting upload url');
	$uid = cut_str($page,"{'uid':'","','status'");

	
	$post['key']= $uid;
	$post['title']= $_REQUEST['title'];
	$post['description']= $_REQUEST['description'];	
	$post['tags']=$_REQUEST['tags'];
	$post['category']=$_REQUEST['category'];
	$post['vid-ex']=$_REQUEST['vid-ex'];
	$post['privacy']=$_REQUEST['privacy'];
	$post['vid-src']='on';
	
	$url=parse_url('http://mds04.milledrive.com/videos/upload/?X-Progress-ID='.$uid);
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"], 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $refup, $cookies, $post, $lfile, $lname, "vid-file");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
		
	$t1 = "http://milledrive.com/progress?key=$uid&load=$servup";
	$Url=parse_url($t1);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://milledrive.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
		
	$t2 = "http://milledrive.com/upload/info/?key=$uid&mode=V";
	$Url=parse_url($t2);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://milledrive.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
		
	$ddl = cut_str($page,"{'url':'","'}");
	
	$download_link = $ddl;
	}
	
	
elseif($_POST['AV'] == "file")
  {
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID (File)</div>
<?php

	$ref='http://milledrive.com/upload/';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://milledrive.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$upfrm = cut_str($page,'name="file-upl-frm" enctype="multipart/form-data" action="','"');
	$refup = 'http://'.cut_str($upfrm,'http://','upload/');
		
	$ref='http://milledrive.com/uuid?churl=V';
	$Url=parse_url($ref);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://milledrive.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_notpresent($page, 'HTTP/1.1 301 MOVED PERMANENTLY', 'Error getting upload url');
	
	$t1 = 'http://milledrive.com/uuid/?churl=V';
	$Url=parse_url($t1);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://milledrive.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	is_notpresent($page, 'HTTP/1.1 200 OK', 'Error getting upload url');
	$uid = cut_str($page,"{'uid':'","','status'");
		
	$url=parse_url($upfrm."?X-Progress-ID=$uid");
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$refup, $cookies, $post, $lfile, $lname, "file");
	
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	is_page($upfiles);
	
	$t2 = "http://milledrive.com/upload/info/?key=$uid&mode=F";
	$Url=parse_url($t2);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 'http://milledrive.com/', $cookies, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	
	$ddl = cut_str($page,"{'url':'","'wait':'false'");
	
	$download_link = $ddl;
	}
	}
// Made by Baking 03/08/2009 18:47
?>