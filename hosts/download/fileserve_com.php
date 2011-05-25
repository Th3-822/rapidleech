<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}

class fileserve_com extends DownloadClass {

	public function Download($link) {
		global $premium_acc;
                if (strstr($link,"list")){
                $page = $this->GetPage( $link, 0, 0, 0);
                is_page($page);
                preg_match_all("%href=\"\/file\/([0-9A-Za-z]+)%i", $page, $fs, PREG_SET_ORDER);
                $all_fs = array();
                foreach($fs as $link2)
                {
                $all_fs[] = str_ireplace("href=\"", "", "http://www.fileserve.com$link2[0]");
                }
                if(!$all_fs){
                html_error('File not found on Fileserve.com. Please check the download link!');
                }
                $Href=str_replace("'","http://www.fileserve.com",$all_fs);
                if (!is_file("audl.php")) html_error('audl.php not found');
	        echo "<form action=\"audl.php?GO=GO\" method=post>\n";
	        echo "<input type=hidden name=links value='".implode("\r\n",$all_fs)."'>\n";
	        foreach (array ("useproxy","proxy","proxyuser","proxypass") as $v)
		echo "<input type=hidden name=$v value=".$_GET[$v].">\n";
	        echo "<script language=\"JavaScript\">void(document.forms[0].submit());</script>\n</form>\n";
	        flush();
	        exit();
                }

                if ( ($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
		   ( $_REQUEST ["premium_acc"] == "on" && $premium_acc ["fileserve_com"] ["user"] && $premium_acc ["fileserve_com"] ["pass"] ) ) 
                      
		{
			$this->DownloadPremium($link);
                }
                else
                {
                        $this->DownloadFree($link);
                }               	
	}
	private function DownloadFree($link) {
                global $Referer,$options;

                if( $_POST['wait'] == "wait") {
                $link= $_POST['link'];
                $cookie= $_POST['cookie'];
		$post["downloadLink"]="show";
                $page = $this->GetPage($link,$cookie,$post,$Referer."\r\nX-Requested-With: XMLHttpRequest");
                is_page($page);
                unset($post);
		$post["download"]="normal";
                $page = $this->GetPage($link,$cookie,$post,$Referer);
                is_page($page);
                is_present($page,"Your download link has expired.", "Your download link has expired!. Please try again later!.", 0);
                if (stristr ( $page, "Location:" )) {
                $Href = trim ( cut_str ( $page, "Location: ", "\n" ) );
                $Url=parse_url($Href);
	        $FileName = basename($Url["path"]);
                //if (function_exists(encrypt) && $cookie!="") {$cookie=encrypt($cookie);};
                $this->RedirectDownload($Href,$FileName,$cookie);exit;
                }else{html_error ( "Download link not found", 0 );}  
                }

                if( $_POST['captcha'] == "ok") {
		$post["recaptcha_challenge_field"] =$_POST["recaptcha_challenge_field"];
		$post["recaptcha_response_field"] = $_POST["recaptcha_response_field"];
                $post["recaptcha_shortencode_field"] =$_POST["recaptcha_shortencode_field"];
                $link = $_POST["link"];
                $cookie = $_POST["cookie"];
		$page = $this->GetPage("http://www.fileserve.com/checkReCaptcha.php",$cookie,$post,$Referer."\r\nX-Requested-With: XMLHttpRequest");
		is_page($page);
                if (stristr($page,'error":"incorrect-captcha-sol"')){
                echo  ("<center><font color=red><b>Entered code was incorrec. Please re-enter</b></font></center>");
                }
                if (stristr($page,'success":1')){
                unset($post);
                $post["downloadLink"] = "wait";
                $page = $this->GetPage($link,$cookie,$post,$Referer."\r\nX-Requested-With: XMLHttpRequest");
                is_page($page);
                preg_match('/\r\n(.*)\r\n(.*)\r\n0/i', $page, $match);
                $countDown = $match[2];
                if ($countDown) {                
                ?>
                <center><div id="cnt"><h4>ERROR: Please enable JavaScript.</h4></div></center>
                <form action="<?php echo $PHP_SELF; ?>" method="post">
                <input type="hidden" name="link" value="<?php echo $link; ?>">
                <input type="hidden" name="cookie" value="<?php echo $cookie; ?>">
                <input type="hidden" name="wait" value="wait">
                <script language="JavaScript">
                var c = <?php echo $countDown; ?>;
                fcwait();
                function fcwait() {
	        if(c>0) {
		if(c>60){dt ="<font color=red>You reached your traffic limit_FileServe Free User</font>";}else{dt ="<font color=yellow>FileServe Free User</font>";}
		document.getElementById("cnt").innerHTML = "<b>" + dt + "</b><br>Please wait <b>" + c + "</b> seconds...";
		c = c - 1;
		setTimeout("fcwait()", 1000);
		}else {
		document.getElementById("cnt").style.display="none";
		void(document.forms[0].submit());}
	        }
                </script>
                </form></body></html>
                <?php
                exit;}}
                }
                
                $page = $this->GetPage( $link, 0, 0, 0);
                is_page($page);
                $cookie = GetCookies($page);
                is_present($page,"File not available", "File not available. Please check download link!", 0);
                $k = cut_str ( $page ,"var reCAPTCHA_publickey='","';");
                $post["checkDownload"] = "check";
		$page = $this->GetPage( $link, $cookie, $post, $Referer."\r\nX-Requested-With: XMLHttpRequest");
                is_page($page);
                is_present($page,"timeLimit", "Your download link has expired!. Please try again later!.", 0);
                is_present($page,"captchaFail", "Your IP has failed the captcha too many times. Please retry later.!", 0);
                $showCaptcha = cut_str ( $page ,'success":"','"}');
                if($showCaptcha =="showCaptcha"){
                $recaptcha_shortencode_field = cut_str($link, 'file/', '/');
                $recaptcha_shortencode_field = str_replace("/", "", $recaptcha_shortencode_field);
                ?>
                <form action="" method="post">
                <br>
                <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=<?php echo $k;?>"></script>
                <noscript>
                <iframe src="http://www.google.com/recaptcha/api/noscript?k=<?php echo $k;?>"
                height="300" width="500" frameborder="0"></iframe><br>
                <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
                <input type="hidden" name="recaptcha_response_field" value="manual_challenge">  
                </noscript>
                <br>
                <input type="hidden" name="recaptcha_shortencode_field" value="<?php echo $recaptcha_shortencode_field;?>">
                <input type="hidden" name="link" value="<?php echo $link;?>">
                <input type="hidden" name="cookie" value="<?php echo $cookie;?>">
                <input type="hidden" name="captcha" value="ok">
                <input type="Submit" name="Submit" value="Download Now" >
                </form> 
                <?php
                exit;
                }   
	}
	private function DownloadPremium($link) {
		global $Referer, $premium_acc;
                
                $page = $this->GetPage( "http://www.fileserve.com/index.php", 0, 0, "http://www.fileserve.com/index.php");
                is_page($page);
                $cookie = GetCookies($page);
                $post = array ();
                $post ["loginUserName"] = $_GET ["premium_user"] ? $_GET ["premium_user"] : $premium_acc ["fileserve_com"] ["user"];
                $post ["loginUserPassword"] = $_GET ["premium_pass"] ? $_GET ["premium_pass"] : $premium_acc ["fileserve_com"] ["pass"];
                $post ["autoLogin"] = 'on';
                $post ["loginFormSubmit"] = 'Login';
                $page = $this->GetPage( "http://www.fileserve.com/login.php", $cookie, $post, "http://www.fileserve.com/index.php");
                is_page($page);
                $cookie = GetCookies($page);
                unset($post);
		$post["download"]="premium";
                $page = $this->GetPage( $link, $cookie, $post, $Referer);
                is_page($page);
                is_present($page,"File not available", "File not available. Please check download link!", 0); 
                $cookie = GetCookies($page);
                if (stristr ( $page, "Location:" )) {
                $Href = trim ( cut_str ( $page, "Location: ", "\n" ) );
                $Url=parse_url($Href);
	        $FileName = basename($Url["path"]);
                //if (function_exists(encrypt) && $cookie!="") {$cookie=encrypt($cookie);};
                $this->RedirectDownload($Href,$FileName,$cookie);
                }else{
                html_error ( "Download link not found", 0 );
                }   
        }     
}
// Written by VinhNhaTrang 24.12.2010
?>