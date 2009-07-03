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
		$video_t = ""; $video_id = ""; $title = ""; $refmatch = "";
		preg_match ( '/"t": "([^\"]+)/', $this->page, $video_t );
		preg_match ( '/"video_id": "([^\"]+)/', $this->page, $video_id );
		if (preg_match ( "/<title>YouTube - ([^<]+)/", $this->page, $title )) {
			$FileName = str_replace ( Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|" ), "_", html_entity_decode ( $title [1] ) ) . "_" . (isset ( $_POST ["ytube_mp4"] ) ? "HQ.mp4" : "LQ.flv");
		}
		//echo $FileName; exit;
		$cookies = GetCookies ( $this->page );
		preg_match ( '%var swfUrl = canPlayV9Swf\(\) \? "(.+)\.swf" :%U', $this->page, $refmatch );
		$furl = "http://www.youtube.com/get_video?video_id=" . $video_id [1] . "&t=" . $video_t [1] . (isset ( $_POST ["ytube_mp4"] ) && isset ( $_POST ['yt_fmt'] ) ? "&el=detailpage&ps=&fmt=$_POST[yt_fmt]" : "");
		// Add the force_name this way:
		$params = array('force_name' => $FileName);
		$this->RedirectDownload ( $furl, $FileName, $cookies, 0, $refmatch [1], "", $params );
	}
}
?>