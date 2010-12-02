<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}

class filefactory_com extends DownloadClass {
	
	public function Download($link) {
		global $premium_acc;
		
		if ( ($_REQUEST ["premium_acc"] == "on" && $_REQUEST ["premium_user"] && $_REQUEST ["premium_pass"]) ||
			($_REQUEST ["premium_acc"] == "on" && $premium_acc ["filefactory_com"]["user"] && $premium_acc ["filefactory_com"]["pass"] ) )
		{
			$this->DownloadPremium($link);
		}
		else
		{
			$this->DownloadFree($link);
		}
	}
	
	private function DownloadFree($link) 
	{
		global $Referer,$options;
                
		if( $_POST['captcha'] == "ok") 
		{
			$post["recaptcha_challenge_field"] =$_POST["recaptcha_challenge_field"];
			$post["recaptcha_response_field"] = $_POST["recaptcha_response_field"];
			$post["recaptcha_shortencode_field"] =$_POST["recaptcha_shortencode_field"];
			$page = $this->GetPage("http://www.filefactory.com/file/checkCaptcha.php",0,$post,$Referer); 
			is_page($page);
               
			if (stristr($page,'status:"ok"'))
			{
				$path_link = cut_str ( $page, 'path:"','"}');
				$linknew = "http://www.filefactory.com$path_link";
				$page = $this->GetPage($linknew,0,0,$Referer);
				$cookie = GetCookies($page);
				$wait = cut_str($page,'<span class="countdown">','</span>'); 
				insert_timer($wait);
                preg_match('/http:\/\/.+filefactory\.com\/dl\/f\/[^\'"]+/i', $page, $link);
				$file = $link[0];
				$Href = $file;
				$Url = parse_url($Href);
                $FileName = !$FileName ? basename($Url["path"]) : $FileName; 
                $this->RedirectDownload($Href,$FileName);
				exit();
			}
		}
		
		if($_POST['captcha'] == "ok")
		{
			echo ("<center><font color=red><b>Entered code was incorrec. Please re-enter</b></font></center>");
		}

		$page = $this->GetPage($link,0,0,$Referer);
		is_page($page);
		is_present($page,"We have detected several recent attempts to bypass our free download restrictions originating from your IP Address","Free download restrictions originating from your IP Address");
		is_present($page, 'File Not Found', 'Error - File was not found!');

		$ch = cut_str ( $page ,'Recaptcha.create("' ,'"' );
		$check_cap = cut_str ( $page, "check:'","'\n" );
		$recaptcha_shortencode_field = "undefined&check=$check_cap";
		?>
		<form action="" method="post">
		<br>
		<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=<?php echo $ch;?>"></script>
		<noscript>
		<iframe src="http://www.google.com/recaptcha/api/noscript?k=<?php echo $ch;?>"
		height="300" width="500" frameborder="0"></iframe><br>
		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
		<input type="hidden" name="recaptcha_response_field" value="manual_challenge">  
		</noscript>
		<br>
		<input type="hidden" name="recaptcha_shortencode_field" value="<?php echo $recaptcha_shortencode_field;?>">
		<input type="hidden" name="link" value="<?php echo $link;?>">
		<input type="hidden" name="captcha" value="ok">
		<input type="Submit" name="Submit" value="Download Now" >
		</form> 
		<?php
		exit();
	}
	
	private function DownloadPremium($link) 
	{
		global $Referer, $premium_acc;
		
		$Referer = $link;
		$page = $this->GetPage( $link );
		is_page($page);
		is_present($page,"We have detected several recent attempts to bypass our free download restrictions originating from your IP Address","Free download restrictions originating from your IP Address");
		is_present($page, 'File Not Found', 'Error - File was not found!');

		$post = array();
		$post['redirect'] = "%2F%3Flogout%3D1";
		$post['email'] = $_REQUEST["premium_user"] ? trim($_REQUEST["premium_user"]) : $premium_acc["filefactory_com"]["user"]  ;
		$post['password'] = $_REQUEST["premium_pass"] ? trim($_REQUEST["premium_pass"]) : $premium_acc["filefactory_com"]["pass"];
		$page = $this->GetPage("http://www.filefactory.com/member/login.php" ,0 ,$post, "http://www.filefactory.com/?logout=1");
		if (!preg_match('%(ff_membership=.+); expires%', $page, $match)) html_error('Not logged in please check your credentials in config.php', 0);
		$cookie = $match[1];
		$page = $this->GetPage( $link, $cookie, 0, $Referer );
                
		if (stristr ( $page, "Location:" )) 
		{
			$Href=cut_str ($page ,"Location: ","\r");
			$Url=parse_url($Href);
			$FileName=basename($Url["path"]);             
			$this->RedirectDownload($Href,$FileName,$cookie);exit;
		}
		else
		{
			html_error ( "Download link not found", 0 );
		}
	}
}
// Written by VinhNhaTrang 30.11.2010
?>