<?php
// Default Account Info =================
$rapidgator_email = ""; // Set you email username
$rapidgator_pass = ""; //  Set your password
//=======================================

			$not_done = true;
			$continue_up = false;
			if ($rapidgator_email & $rapidgator_pass) {
				$_REQUEST['rg_email'] = $rapidgator_email;
				$_REQUEST['rg_pass'] = $rapidgator_pass;
				$_REQUEST['action'] = "FORM";
				echo "<b><center>Automatic Login Rapidgator.net</center></b>\n";
			}
			if ($_REQUEST['action'] == "FORM"){
				$continue_up = true;
			}else {
?>
					<script>document.getElementById('info').style.display='none';</script>
                    <div id='info' width='100%' align='center' style="font-weight:bold; font-size:16px">LOGIN</div> 
    <table border=0 style="width:270px;" cellspacing=0 align=center>
        <form method="post">	
        <input type='hidden' name='action' value='FORM' />
            <tr><td nowrap>&nbsp;User Email*<td>&nbsp;<input type='text' name='rg_email' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='rg_pass' value='' style="width:160px;" />&nbsp;</tr>
            <tr><td colspan=2 align=center><input type='submit' value='Upload' /></tr>
            <tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["rapidgator.net_member"]; ?></b></small></tr>
        </form>
    </table>
<?php
			}
			if ($continue_up) {
				$not_done = false;
?>
    <table width=600 align=center>
    </td></tr>
    <tr><td align=center>
    		<script>document.getElementById('info').style.display='none';</script>
            <div id='info' width='100%' align='center'>Login to rapidgator.net</div>
<?php
        if (!empty($_REQUEST['rg_email']) && !empty($_REQUEST['rg_pass'])){
        $Url = parse_url("http://www.rapidgator.net/auth/login/");
        $post["LoginForm[email]"] = $_REQUEST['rg_email'];
        $post["LoginForm[password]"] = $_REQUEST['rg_pass'];
        $post["LoginForm[rememberMe]"] = "1";
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.rapidgator.net/", 0, $post, 0, $_GET["proxy"], $pauth);
		is_page($page);
		$cookie = GetCookies($page);
		preg_match('#HTTP\/1\.1[\r|\n|\s]+([0-9]+)[\r|\n|\s]#', $page, $pg);
		if($pg[1] == '400'){
			html_error('Necessary to update the plugin');
		}
		preg_match('#Location:[\r|\n|\s]+([a-z\/]+)#', $page, $blo);
		if($blo[1] == '/auth/login/'){
			html_error('You made several unsuccessful login attempts and access was blocked. Please wait 3 hours 30 minutes and try again');
		}
		if(!preg_match('#user__=([0-9a-zA-Z%.]+)#', $cookie, $user)){
				html_error ('Error e-mail or password. Warning: more than 17 unsuccessful login attempts your access will be blocked.');
		}
		if(empty($cookie)){
			html_error('Trouble in Login, please try again later');
		}
		}else{
		html_error ('Error, user and/or password is empty, please go back and try again!');
		}
?>
        		<script>document.getElementById('info').style.display='none';</script>
				<div id='info' width='100%' align='center'>Retrive upload ID</div> 
<?php 
		$page = geturl("www.rapidgator.net", 80, "/", 0, $cookie);
		is_page($page);
		if(!preg_match('#var[\r|\n|\s]+form_url[\r|\n|\s]+=[\r|\n|\s]+"([^"]+)"#', $page, $up)){
			html_error ('Cannot get form url upload.', 0);
		}
		if(!preg_match('#progress_url_srv[\r|\n|\s]+=[\r|\n|\s]+"([^"]+)"#', $page, $pr)){
			html_error ('Cannot get url progress upload.', 0);
		}
		$uuid = id();
		$url = parse_url($up[1].$uuid);
		$lname = remover($lname);
		$ur = $pr[1].'&data%5B0%5D%5Buuid%5D='.$uuid.'&data%5B0%5D%5Bstart_time%5D='.time();
		$upfiles  = upfile($url['host'], defport($url), $url['path'] . ($url['query'] ? '?' . $url['query'] : ''), 'http://www.rapidgator.net/', $cookie, 0, $lfile, $lname, 'file');
?>
				<script>document.getElementById('progressblock').style.display='none';</script>
<?php
		$Url = parse_url($ur);
		$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"],$pauth);
		preg_match('#{"state":"([^"]+)#', $page, $msg);
		if($msg[1] == 'uploading'){
			html_error('Erro in upload: Code 01', 0);
		}
		if($msg[1] == 'error'){
			html_error('Erro in upload: Code 02', 0);
		}
		preg_match('#"download_url":"([^"]+)#', $page, $dl);
		preg_match('#"remove_url":"([^"]+)#', $page, $del);
		$dll = str_replace("\\","",$dl[1]);	
		$dele = str_replace("\\","",$del[1]);	
			if (!empty($dll))
			$download_link =  $dll;
			else
              html_error ('Didn\'t find downloadlink!');
			if (!empty($dele))
			$delete_link =  $dele;
			else
              html_error ('Didn\'t find deletelink!');
	}
?>

<?php
function remover($str){
			$cod = "UTF-8";
			$car = array(
			'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
			'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
			'C' => '/&Ccedil;/',
			'c' => '/&ccedil;/',
			'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
			'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
			'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
			'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
			'N' => '/&Ntilde;/',
			'n' => '/&ntilde;/',
			'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
			'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
			'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
			'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
			'Y' => '/&Yacute;/',
			'y' => '/&yacute;|&yuml;/',
			'' => '/&yen;|&sect;|&uml;|&lt;|&gt;|&not;|&acute;|&deg;|&cent;|&curren;|&copy;|&laquo;|&reg;|&plusmn;|&micro;|&para;|&raquo;|&iquest;|&divide;|&iexcl;/',
			'a.' => '/&ordf;/',
			'o.' => '/&ordm;/', 
			'_' => '/([% +&$#@!,\][?`^~;:\{}|=*\/]+)/',
			);

   return preg_replace($car, array_keys($car), htmlentities($str,ENT_NOQUOTES, $cod));
   // function by simplesdescarga 06/02/2012
}
	function id(){
				$ext = "0123456789";
				$hex = "0123456789abcdef";
				$let = str_split($hex,1);
				$contbase = strlen($hex);
				$comple = '';
						for($i=0; $i < 46; $i++){
						$rand .= $ext{mt_rand() % strlen($ext)};
						}
				$base = $rand;
				for ($i = 0; $i < 32; $i++) {
					$comple = $let[bcmod($base,$contbase)].$comple;
					$base = bcdiv($base,$contbase,0);
				}
			return $comple;
	//function by simplesdescarga 02/02/2012
	}
	//upload plugin produced by simplesdescarga day 03/01/2011 at 18:47.
?>