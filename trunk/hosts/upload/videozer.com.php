<?php
####### Account Info. ###########
$videozer_login = '';
$videozer_pass = '';
##############################

				$not_done=true;
				$continue_up=false;
				if ($videozer_login && $videozer_pass){
					$_REQUEST['videozer_login'] = $videozer_login;
					$_REQUEST['videozer_pass'] = $videozer_pass;
					$_REQUEST['action'] = "FORM";
					echo "<b><center>Use Default login/pass.</center></b>\n";
				}
				if ($_REQUEST['action'] == "FORM")
					$continue_up=true;
				else{
?>
					<script>document.getElementById('info').style.display='none';</script>
            <div id='info' width='100%' align='center' style="font-weight:bold; font-size:16px">LOGIN</div>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;User*<td>&nbsp;<input type=text name=videozer_login style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=videozer_pass style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
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
						<script>document.getElementById('info').style.display='none';</script>
						<div id=login width=100% align=center>Login to Videobb.com</div>
<?php
				$ext = extensao($lname);
				if (!empty($_REQUEST['videozer_login']) && !empty($_REQUEST['videozer_pass'])) {
				if($ext == 'avi' || $ext == 'divx' || $ext == '3gp' || $ext == 'mov' ||  $ext == 'mpeg' ||  $ext == 'mpg' ||  $ext == 'xvid' ||  $ext == 'flv' ||  $ext == 'asf' ||  $ext == 'dat' ||  $ext == 'mp4' ||  $ext == 'mkv' || $ext == 'f4v' || $ext == 'm4v' || $ext == 'rm' || $ext == 'wmv'){
                $rnd = time().rndNum(3);
				$page = geturl("videobb.com", 80, "/index.php","http://videozer.com/upload.php",0, 0, 0);
		        is_page($page);
                $cookie = GetCookies($page);
				$split = split ('[; ]', $cookie);
				$cookie = $split[0].'; '.$split[18];
                $post = array();
		        $post['login_username'] = trim($_REQUEST['videozer_login']);
		        $post['login_password'] = trim($_REQUEST['videozer_pass']);
				$post['rem_me'] = '';
				$page = geturl("videozer.com", 80, "/login.php","http://videozer.com/index.php",$cookie, $post, 0);
		        is_page($page);
                preg_match('/^HTTP\/1\.0|1 ([0-9]+) .*/',$page,$status);
				if ($status[1] == 200) {html_error("Error, check username and password", 0);}
                $cookie .= "; ".GetCookies($page);
				$split = split ('[; ]', $cookie);
				$cookie = $split[0].'; '.$split[2].'; '.$split[12].'; '.$split[14];
				if($split[0] == '' || $split[2] == '' || $split[12] == '' || $split[14] == ''){
					html_error('Error in login, try again. Or needs to be updated!');
					}
					}else{
				html_error('This video is not allowed to Videobb.com. Video allowed: avi, divx, 3gp, mov, mpeg, mpg, xvid, flv, asf, dat, mp4, mkv, wmv, rm, m4v, f4v');}
				}else{
					html_error('User or password is empty, check and try again');
				}
?>
					<script>document.getElementById('login').style.display='none';</script>
					<div id=info width=100% align=center>Retrive upload ID</div>
<?php
            $page = geturl("videozer.com", 80, "/ajax/upload_track.php?cat_sel=1&f=$lname","http://videozer.com/upload.php\r\nX-Requested-With: XMLHttpRequest",$cookie, 0, 0);
		    is_page($page);
            $sessionId=urlencode(cut_str ( $page ,'sessionId":"' ,'",'));
            $sid=cut_str ( $page ,'sid":"' ,'",');
            $sd=cut_str ( $page ,'sd":"' ,'",');
            $ip=cut_str ( $page ,'ip":"' ,'",');
            $cc=cut_str ( $page ,'cc":"' ,'",');
			$vc=cut_str ( $page ,'vc":"' ,'"');
			$enc = cut_str($page ,'enc":"' ,'",');
            $progressURL=urldecode(cut_str ( $page ,'progressURL":"' ,'",'));
            $submitURL=urldecode(cut_str ( $page ,'submitURL":"' ,'",'));
            $URL = htmlspecialchars_decode($progressURL);
           	$URL=str_replace("\\","",$URL);
            $upurl=htmlspecialchars_decode($submitURL);
			$upurl=str_replace("\\","",$upurl);
			                                                    
?>
					<script>document.getElementById('info').style.display='none';</script>
					<div id=info width=100% align=center>Connecting Upload</div> 
<?php 
			$page = geturl("videozer.com", 80, "/upload.php","http://videozer.com/upload.php",$cookie, 0, 0);
		    is_page($page);  
            $upurl = $upurl."?X-Progress-ID=$sessionId";
			$url=parse_url($upurl);
            unset($post);
			$post=array();
            $post["uploadSessionId"] = $sessionId;
			$post["APC_UPLOAD_PROGRESS"] = $sessionId;
            $post["ud"]=$ud;
			$post["un"]=$un;
			$post["enc"]=$enc;
            $post["broadcast"]="0";
           	$post["allow_comments"]= "0";
			$post["allow_rating"]="0";
            $post["search_by_user"]= "0";
			$post["title"]="Upload Video, thanks simplesdescarga";
            $post["description"]="Upload by simplesdescarga";
            $post["cat_sel"]="1";
			$post["sip"]=$sip;
			$post["ps"]="1";
            $post["cc"]=$cc;
         	$post["bsid"]= "7";
			$post["sid"]=$sid;
            $post["ip"]= $ip;
			$post["vc"]=$vc;
            $post["slip"]=$slip;
            $post["sd"]="WU";
			$upfiles = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), 0, $cookie, $post, $lfile, $lname, "file");
			is_page($upfiles);
?>
							<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
            $upurl = $URL."/progress?X-Progress-ID=$sessionId&callback=jsonp$rnd";
            $Url=parse_url($upurl);
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""),"http://videozer.com/upload.php", $cookie, 0, 0, $_GET["proxy"],$pauth);
            is_page($page);
            $state=cut_str ( $page ,'state" : "' ,'"');
            if($state == "starting"){
            $download_link= "http://www.videozer.com/video/$vc";
           	}else {
           	html_error ('Error upload file');}
	}
?>

<?php		
				//Função que verifica a extensão
				function extensao($file){
				$tam = strlen($file);
				if( $file[($tam)-4] == '.' ){
				$extensao = substr($file,-3);}
				elseif( $file[($tam)-5] == '.' ){
				$extensao = substr($file,-4);}
				elseif( $file[($tam)-3] == '.' ){
				$exe = substr($file,-2);
				}else{
				$exe = NULL;}
				return $extensao;}
				
				function rndNum($lg){
				$str="0123456789"; 
				for ($i=1;$i<=$lg;$i++){
				$st=rand(1,9);
				$pnt.=substr($str,$st,1);}
				return $pnt;}
				
							//VinhNhaTrang 17/12/2010
							//hackinho 06/07/2011
							//fixados os cookies by simplesdescarga 13/01/2012
							//adiconado suporte a extenção de arquivos by simplesdescarga 13/01/2012
							//fixados erros no login by simplesdescarga 13/01/2012
							//100% funcionando o upload by simplesdescarga 13/01/2012
?>