<?php

####### Free Account Info. ###########
$minhateca_login = ""; //set your id (login)
$minhateca_pass = ""; //set your  password

##############################

$host="minhateca.com.br";

$not_done=true;
$continue_up=false;
if ($minhateca_login && $minhateca_pass){
        $_REQUEST['my_login'] = $minhateca_login;
        $_REQUEST['my_pass'] = $minhateca_pass;
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
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["minhateca.com.br"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to <?php echo $host; ?></div>
<?php 
                        $page = geturl($host, 80, "/", "http://".$host."/", 0, 0, 0, $_GET["uproxy"], $pauth);
                        is_page($page);
						$cookie = GetCookies($page);
						
						preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
						
						$csrf_token = str_replace('__RequestVerificationToken_Lw__=', '', $cookie);
						
						$csrf_token = str_replace('+', '%2B', $csrf_token);
						$csrf_token = str_replace('/', '%2F', $csrf_token);
						
						$post['FileId'] = "0";						
						$post['Login'] = $_REQUEST['my_login'];
                        $post['Password'] = $_REQUEST['my_pass'];
                        $post['RememberMe'] = "false";
                        $post['redirect'] = "true";
                        $post['redirectUrl'] = "";
						
                        $post['__RequestVerificationToken'] = $csrf_token;
                        						
						$page = geturl($host, 80, "/action/Login/login", "http://".$host."/", $cookie, $post, 0, $_GET["uproxy"], $pauth);
												
                        is_notpresent($page, 'HTTP/1.1 302', 'Error logging in - are your logins correct!');
						
						$cookie = GetCookies($page);
                        $page = geturl($host, 80, "/". $_REQUEST['my_login'], "http://".$host."/", $cookie, 0, 0, $_GET["uproxy"], $pauth);
						
						is_page($page);
						
						$pos = strpos($page, '<!DOCTYPE');
						if($pos == false)
						{
							html_error ('Login error');
						}
						$page = substr($page, $pos);
						
						$dom = new DOMDocument();
						@$dom->loadHTML($page);
						
						$accNoTag = $dom->getElementById('__accno');
						if($accNoTag == NULL)
						{
							html_error('Error logging in - are your logins correct!');
						}
						$accountId = $accNoTag->getAttribute('value');
												
                        is_page($page);
        ?>
<!--<script>document.getElementById('login').style.display='none';</script>-->
<div id=info width=100% align=center>Retrive upload ID</div>
<script>document.getElementById('info').style.display='none';</script>
<?php 
						$path = "/ChomikUploadingFile.aspx?id=" . $accountId. "&sid=0";
						$page = geturl($host, 80, $path, "http://".$host."/", $cookie, 0, 0, $_GET["uproxy"], $pauth);
						
						is_page($page);
						
						$redir = getRedirector($page);
												
						$uploadUrl = parse_url($redir);
						
						$uploadPath = str_replace("UploadingFileProgress", "UploadingFile", $uploadUrl["path"]);
						
						$page = geturl($uploadUrl["host"],$uploadUrl["port"] ? $uploadUrl["port"] : 80, $uploadPath.($uploadUrl["query"] ? "?".$uploadUrl["query"] : ""), "http://".$host."/", $cookie, 0, 0, $_GET["uproxy"], $pauth);
						
						is_page($page);
						
						$pos = strpos($page, '<!DOCTYPE');
						if($pos == false)
						{
							html_error ('Upload error');
						}
						$page = substr($page, $pos);
						
						$dom = new DOMDocument();
						@$dom->loadHTML($page);
						
						$viewStateTag = $dom->getElementById('__VIEWSTATE');
						$viewState = $viewStateTag->getAttribute('value');

						$uploadPost = array();
						$uploadPost['__EVENTTARGET'] = 'UploadButton';
						$uploadPost['__EVENTARGUMENT'] = '';
						$uploadPost['RegulationsOwner'] = 'on';
						$uploadPost['FileSampleDescription'] = '';
						$uploadPost['__VIEWSTATE'] = $viewState;
						$uploadPost['APC_UPLOAD_PROGRESS'] = $progress_key;
                        $uploadPost['APC_UPLOAD_USERGROUP'] = $usergroup_key;
                        $uploadPost['UPLOAD_IDENTIFIER'] = $progress_key;
						
						$uploadPath = str_replace("UploadingFileProgress", "UploadingFile", $uploadUrl["path"]);
												
                        $upfiles = upfile($uploadUrl["host"],$uploadUrl["port"] ? $uploadUrl["port"] : 80, $uploadPath.($uploadUrl["query"] ? "?".$uploadUrl["query"] : ""), "http://".$host."/", $cookie, $uploadPost, $lfile, $lname, "File1", "", $_GET["uproxy"]);
						
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php   
						is_page($upfiles);
												
						is_notpresent($upfiles, 'HTTP/1.1 200', 'Upload error');
						
						$pos = strpos($upfiles, '<!DOCTYPE');
						if($pos == false)
						{
							html_error ('Upload error');
						}
						$upfiles = substr($upfiles, $pos);
						
						$dom = new DOMDocument();
						@$dom->loadHTML($upfiles);
						
						$uploadConfirmTag = $dom->getElementById('UploadedConfirm');
						if($uploadConfirmTag == NULL)
						{
							html_error ('Upload error');
						}
        }

  
 function getRedirector($page) {
    preg_match('/Location: ([^\r|\n]+)/i', $page, $redir);
    return $redir[1];

}
// written by VinhNhaTrang 04/11/2010
?>
