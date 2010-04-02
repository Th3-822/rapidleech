<?php
if (! defined ('RAPIDLEECH'))
{
	require_once ("index.html");
	exit ();
}

class youtube_com extends DownloadClass
{
	private $page;
	
	public function Download($link)
	{
		$this->page = $this->GetPage($link);
		if (!preg_match('#fmt_url_map=(.+?)&#', $this->page, $fmt_url_map)) html_error('Video link not found.');
		$fmt_url_maps = preg_split('%,%', urldecode($fmt_url_map[1]));
		$fmts = array(37,22,35,18,34,6,5,0,17,13);
		$yt_fmt = $_POST['yt_fmt'];

		if ($_POST['ytube_mp4'] == 'on')
		{
			foreach ($fmt_url_maps as $fmtlist)
			{
				$furlmap = preg_split('%\|%', $fmtlist);
				$fmturlmaps[$furlmap[0]] = $furlmap[1];
			}

			//look for and download the highest quality we can find?
			if ($yt_fmt == 'highest')
			{
				foreach ($fmts as $fmt)
				{
					if (in_array($fmt, array_keys($fmturlmaps)))
					{
						$furl = $fmturlmaps[$fmt];
						break;
					}
				}
			}
			else //get the format the user specified (making sure it actually exists)
			{
				if (!$furl = $fmturlmaps[$yt_fmt])
				{
					preg_match ('/"t": "([^\"]+)/', $this->page, $video_t);
					preg_match ('/"video_id": "([^\"]+)/', $this->page, $video_id);
					preg_match ('%var swfUrl = canPlayV9Swf\(\) \? "(.+)\.swf" :%U', $this->page, $refmatch);
					$cookies = GetCookies ($this->page);
					$gurl = "http://www.youtube.com/get_video?video_id=" . $video_id [1] . "&t=" . $video_t [1] . "&el=detailpage&ps=&fmt=$yt_fmt";
					$page = $this->GetPage ($gurl, $cookies, 0, $refmatch [1]);
					if (! preg_match ('%ocation: (.+)\r\n%', $page, $durl)) html_error ('Specified video format not found');
					$furl = $durl[1];
				}
			}
		}
		else //just get the one Youtube plays by default (in some cases it could also be the highest quality format)
		{
			foreach ($fmt_url_maps as $fmtlist)
			{
				$furlmap = preg_split('%\|%', $fmtlist);
				$fmturlmaps[] = $furlmap;
			}
			$fmt = $fmturlmaps[0][0];
			$furl = $fmturlmaps[0][1];
		}

		if (preg_match ('%0|5|6|34|35%', $yt_fmt)) $ext = '.flv';
		elseif (preg_match ('%18|22|37%', $yt_fmt)) $ext = '.mp4';
		elseif (preg_match ('%13|17%', $yt_fmt)) $ext = '.3gp';
		elseif (preg_match ('%highest%', $yt_fmt)) $ext = '.mp4';
		else $ext = '.flv';

		if (!preg_match('#<title>.*YouTube.*-(.*)</title>#Us', $this->page, $title)) html_error('No video title found! Download halted.');
		if (!$video_id) preg_match ('#video_id=(.+?)&#', $this->page, $video_id);

		$FileName = str_replace (Array ("\\", "/", ":", "*", "?", "\"", "<", ">", "|"), "_", html_entity_decode (trim($title[1]))) . (isset ($_POST ['yt_fmt']) && $_POST ['yt_fmt'] !== 'highest' ? '-[' . $video_id[1] . '][f' . $_POST ['yt_fmt'] . ']' : '-[' . $video_id[1] . '][f' . $fmt . ']') . $ext;
		
		if ($_POST ['ytdirect'] == 'on')
		{
			echo "<br /><br /><h4><a style='color:yellow' href='" . urldecode($furl) . "'>Click here or copy the link to your download manager to download</a></h4>";
			echo "<input name='dlurl' style='width: 1000px; border: 1px solid #55AAFF; background-color: #FFFFFF; padding:3px' value='" . urldecode($furl) . "' onclick='javascript:this.select();' readonly></input>";
		}
		else
		{
			// Add the force_name this way:
			$params = array ('force_name' => $FileName);
			$this->RedirectDownload ($furl, $FileName, $cookies, 0, $refmatch [1], "", $params);
		}
	}
}
//re-written by szal based on original plugin by eqbal
//updated 02 Apr 2010
?>