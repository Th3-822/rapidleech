<?php

####### Account Info. ###########
$skipfile_login = ""; //Set your skipfile.com user
$skipfile_pass = ""; //Set your skipfile.com password
##############################

$not_done=true;
$continue_up=false;
if ($skipfile_login && $skipfile_pass){
	$_REQUEST['my_login'] = $skipfile_login;
	$_REQUEST['my_pass'] = $skipfile_pass;
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
<tr><td nowrap>&nbsp;User*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["skipfile.com"]; ?></b></small></tr>
</table>
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
<div id=login width=100% align=center>Login to Skipfile.com</div>
<?php 
			$page = geturl("skipfile.com", 80, "/", 0, 0, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);			
                        $cookie = GetCookies($page);
                        $page = geturl("skipfile.com", 80, "/login.html", "http://skipfile.com/", $cookie, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);			
                        $cookie = GetCookies($page);
			$post = array();
                        $post['op'] = 'login';
                        $post['redirect'] = 'http%3A%2F%2Fskipfile.com%2F';
			$post['login']  = $_REQUEST['my_login'];
			$post['password'] = $_REQUEST['my_pass'];
                        $post["x"] = rand(1,140);
                        $post["y"] = rand(1,9);
                        $page = geturl("skipfile.com", 80, "/","http://skipfile.com/login.html",$cookie, $post, 0);
			is_page($page);		
                        $cookie = GetCookies($page);
			preg_match('/^HTTP\/1\.0|1 ([0-9]+) .*/',$page,$status);
			if ($status[1] == 200) {html_error("Login error", 0);}
			$page = geturl("skipfile.com", 80, "/", 'http://skipfile.com/login.html', $cookie, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
                        
                        				
?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive test</div>
<script>document.getElementById('info').style.display='none';</script>
<?php 		
                        is_page($page);
                        $up_url = cut_str($page,'enctype="multipart/form-data" action="','" method="post" onSubmit="return StartUpload(this);"');
                        $upload_type = cut_str($page,'upload_type" value="','">');
                        $sess_id = cut_str($page,'sess_id" value="','">');
                        $srv_tmp_url = cut_str($page,'srv_tmp_url" value="','">');
                        $ID =rndNum(12);
                        unset($post);
			$post = array();
			$post['upload_type'] = $upload_type;
                        $post['sess_id']     = $sess_id;
                        $post['srv_tmp_url'] = $srv_tmp_url;
                        $post['file_1"; filename="'] = "";
                        $post['file_0_descr'] = "";
                        $post['link_rcpt'] = "";
                        $post['link_pass'] = "";
                        $post['tos'] = "1";
                        $post['submit_btn'] = "Upload!";
                        $linkul = $up_url.''.$ID.'&js_on=1&utype=reg&upload_type='.$upload_type.'';                        
			$url = parse_url($linkul);
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://skipfile.com/", $cookie, $post, $lfile, $lname, "file_0");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
                        preg_match("%<textarea name='fn'>(.*)</textarea><textarea name='st'>OK</textarea>%", $upfiles, $match);
                        $fn = $match[1];
                        if(!$fn){html_error("Upload failed");}
                        $download_link = 'http://skipfile.com/'.$fn.'/'.$lname;	
                          		
	}
function rndNum($lg){
$str="0123456789"; 
for ($i=1;$i<=$lg;$i++){
$st=rand(1,9);
$pnt.=substr($str,$st,1);
}
return $pnt;
}
//Create by VinhNhaTrang_19.12.2010
?>
