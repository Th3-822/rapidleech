<table width=600 align=center>
    </td></tr>
    <tr><td align=center>
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$url = parse_url('http://filewinds.com/');
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://filewinds.com/", 0, 0, 0, $_GET["proxy"], $pauth);
			$form = cut_str($page, '<form name="file" enctype="multipart/form-data"', '</form>');
            if(!preg_match("#var[\r|\n|\s]+utype='([^']+)'#", $page, $utype)){
				html_error('Cannot get utype');
			}
		    if(!preg_match('#action="([^"]+)"#', $page, $up)){
				html_error('Cannot URL for Upload');
			}
			if(!preg_match_all('#<input[\r|\n|\s]+type="hidden"[\r|\n|\s]+name="([^"]+)"[\r|\n|\s]+value="([^"]+)"#', $form, $dt)){
				html_error('Cannot get data form upload');
			}
			$data = array_combine($dt[1], $dt[2]);
			$uid = uid();
			$url = parse_url($up[1].$uid.'&js_on=1&utype='.$utype[1].'&upload_type=file');
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://filewinds.com/", 0, $post, $lfile, $lname, "file_1");
?>

<script>document.getElementById('progressblock').style.display='none';</script>
<?php
is_page($upfiles);
			preg_match_all("#>([^><]+)<#", $upfiles, $dl);
			if($dl[1][1] != 'OK')
				html_error('Erro in upload');
			$post['fn'] = $dl[1][0];
			$post['st'] = 'OK';
			$post['op'] = 'upload_result';
			$page = geturl("filewinds.com", 80, "/", 'http://filewinds.com/', 0, $post, 0, $_GET["proxy"], $pauth);
				if(!empty($dl[1][0]))
					$download_link = 'http://filewinds.com/'.$dl[1][0].'/'.$lname.'.html';
				else
					html_error ("Didn't find download link!");

function uid(){
				$nu = "0123456789";
				for($i=0; $i < 12; $i++){
				$rand .= $nu{mt_rand() % strlen($nu)};
				}
				return $rand;
				//function by simplesdescraga 05/02/2012
	}
/**
written by SD-88 19.04.2013
**/   
?>