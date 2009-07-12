<?php
if (! defined ( 'RAPIDLEECH' )) {
	require_once ("index.html");
	exit ();
}

class youtube_com extends DownloadClass {
	private $page;
	private $cookie;
	
	public function Download($link) {
		$this->page = $this->GetPage ( $link );
		
		// Initialize the variables
		$video_t = "";
		$video_id = "";
		$title = "";
		$refmatch = "";
		preg_match ( '/"t": "([^\"]+)/', $this->page, $video_t );
		preg_match ( '/"video_id": "([^\"]+)/', $this->page, $video_id );
		
		if ($yt_fmt = $_POST ['yt_fmt']) {
			if (preg_match ( '%0|5|6|34|35%', $yt_fmt ))
				$ext = '.flv';
			elseif (preg_match ( '%18|22%', $yt_fmt ))
				$ext = '.mp4';
			elseif (preg_match ( '%13|17%', $yt_fmt ))
				$ext = '.3gp';
		} else
			$ext = '.flv';
		
		if (preg_match ( "/<title>YouTube - ([^<]+)/", $this->page, $title )) {
			$FileName = str_replace ( Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|" ), "_", html_entity_decode ( $title [1] ) ) . (isset ( $_POST ['yt_fmt'] ) ? '_' . $_POST ['yt_fmt'] : '') . $ext; //. "_" . (isset ( $_POST ["ytube_mp4"] ) ? "HQ.mp4" : "LQ.flv");
		}
		
		$cookies = GetCookies ( $this->page );
		preg_match ( '%var swfUrl = canPlayV9Swf\(\) \? "(.+)\.swf" :%U', $this->page, $refmatch );
		$furl = "http://www.youtube.com/get_video?video_id=" . $video_id [1] . "&t=" . $video_t [1] . (isset ( $_POST ["ytube_mp4"] ) && isset ( $_POST ['yt_fmt'] ) ? "&el=detailpage&ps=&fmt=$_POST[yt_fmt]" : "");
		
		if ($_POST ['ytdirect'] == 'on') {
			$this->page = $this->GetPage ( $furl, $cookies, 0, $refmatch [1] );
			if (! preg_match ( '%ocation: (.+)\r\n%', $this->page, $durl ))
				html_error ( 'Video or video format not found' );
			echo "<br /><br /><h4><a style='color:yellow' href='$durl[1]'>Click here or copy the link to your download manager to download</a></h4>";
			echo "<input name='dlurl' style='width: 1000px; border: 1px solid #55AAFF; background-color: #FFFFFF; padding:3px' value='$durl[1]' onclick='javascript:this.select();' readonly></input>";
		} else {
			// Add the force_name this way:
			$params = array ('force_name' => $FileName );
			$this->RedirectDownload ( $furl, $FileName, $cookies, 0, $refmatch [1], "", $params );
		}
	}
}
?>