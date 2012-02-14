<?php

####### Account Info. ###########
$uploaded_user = ""; //  Set you username
$uploaded_pass = ""; //  Set your password
##############################

	$not_done = true;
	$continue_up = false;
				if ($uploaded_user & $uploaded_pass)
				{
					$_REQUEST['ud_user'] = $uploaded_user;
					$_REQUEST['ud_pass'] = $uploaded_pass;
					$_REQUEST['action'] = "FORM";
					echo "<b><center>Automatic Login Uploaded.to.</center></b>\n";
				}
				if ($_REQUEST['action'] == "FORM")
					$continue_up = true;
				else{
?>
					<script>document.getElementById('info').style.display='none';</script>
                    <div id='info' width='100%' align='center' style="font-weight:bold; font-size:16px">Login</div>
    <table border=0 style="width:270px;" cellspacing=0 align=center>
        <form method=post>
            <input type='hidden' name='action' value='FORM' />
            <tr><td nowrap> Username*<td> <input type='text' name='ud_user' value='' style="width:160px;" /> </tr>
            <tr><td nowrap> Password*<td> <input type='password' name='ud_pass' value='' style="width:160px;" /> </tr>
            <tr><td colspan=2 align=center><input type='submit' value='Upload' /></tr>
            <tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["uploaded.to"]; ?></b></small></tr>
    </table>
    </form>

<?php
}
if ($continue_up){
    $not_done = false;
?>
    <table width=600 align=center>
    </td></tr>
    <tr><td align=center>
        	<script>document.getElementById('info').style.display='none';</script>
            <div id='info' width='100%' align='center'>Login to Uploaded.to</div>
<?php
		if (!empty($_REQUEST['ud_user']) && !empty($_REQUEST['ud_pass'])){
        $usr = trim($_REQUEST['ud_user']);
        $pass = trim($_REQUEST['ud_pass']);
        $referrer = "http://uploaded.to/";
        $Url = parse_url('http://uploaded.to/io/login');
        $post['id'] = $usr;
        $post['pw'] = $pass;
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $referrer, 0, $post, 0, $_GET["proxy"], $pauth);
        is_page($page);  
        $cookie = GetCookies($page) . ';lang=en';
		if(empty($cookie)){
		html_error('Problem during login.');
		}
		if(!preg_match('#login=([^=]+);#',$cookie,$pwid)){
			html_error('User and password do not match!');
		}
		if($pwid[1] == '0'){
			html_error('Error during login, check username and password. Error 01');
		}
		if($pwid[1] == ''){
			htm_error('Error during login, check username and password. Error 02');
		}
		if($pwid[1] == 'deleted'){
			html_error('Plugin needs updating.');
		}
		}else{
		html_error ('Error, user and/or password is empty, please go back and try again!');
	}
?>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
		$decode = decode($pwid[1]); 
		$va = explode('/', $decode);
		if(!empty($va[2]) && !empty($va[4])){
			$uid  = $va[2];
			$upas = $va[4];
		}else{
			html_error('Error get User ID and/or User Password.');
		}
        $Url = parse_url("http://uploaded.to/js/script.js");
        $script = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"], $pauth);
        $editKey = generate(6);
        $serverUrl = cut_str($script, 'uploadServer = \'', '\'').'upload?admincode='.$editKey.'&id='.$uid.'&pw='.$upas;
        $Url = parse_url("http://uploaded.to/io/upload/precheck");
        $id = rand(1,15);
        $fileInfo['size'] = filesize($lfile);
        $fileInfo['id'] = 'file'.$id;
        $fileInfo['name'] = $lname;
        $fileInfo['editKey'] = $editKey;
		geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 'http://uploaded.to/upload', $cookie, $fileInfo, 0, $_GET["proxy"], $pauth);
?>
        <script>document.getElementById('info').style.display='none';</script>
<?php
        $url = parse_url($serverUrl);
        $upagent = "Shockwave Flash";
        $fpost['Filename'] = $lname;
        $fpost['Upload'] = 'Submit Query';
		$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://uploaded.to/", $cookie, $fpost, $lfile, $lname, "Filedata", 0, 0, 0, $upagent); 
?>
        <script>document.getElementById('progressblock').style.display='none';</script>
<?php
       if(preg_match('#close\s+([A-Za-z0-9]+),#',$upfiles,$links)){
		   $download_link = "http://ul.to/".$links[1]."/".$lname."";
		   $adm_link = $editKey;
	   }else{
		   html_error("Didn't find download link!");
	   }
    }

		function decode($str){
				$str = str_replace('%3D', '/', $str);
				$str = str_replace('%26', '/', $str);
				return $str;
			//function by simplesdescarga 09/02/2012
		}
    function generate($len) {
        $pwd = '';
        $con = Array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z');
        $voc = Array('a', 'e', 'i', 'o', 'u');
        for ($i = 0; $i < $len / 2; $i++){
            $c = mt_rand(0, 19);
            $v = mt_rand(0, 4);
            $pwd .= $con[$c] . $voc[$v];
        }
        return $pwd;
    }

    /*
      written by kaox 14/06/2009
      edited by Balor 15/03/2011 (new Layout with YUI uploader)
      fixed by defport 27/04/2011
	  fixed user id and password by simplesdescarga 09/02/2012
	  fixed messages error at login and to get user id and password by simplesdescarga 09/02/2012
	  added support for IPs American by simplesdescarga 09/02/2012
    */
?>