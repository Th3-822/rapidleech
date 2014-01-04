<table width=600 align=center> 
</td></tr> 
<tr><td align=center> 
<div id=login width=100% align=center></div> 
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$ext = extensao($lname);
			if($ext == 'php' || $ext == 'pl' || $ext == 'cgi' || $ext == 'py' ||  $ext == 'sh' ||  $ext == 'shtml'){
	html_error('Extension not allowed for file: "'.$lname.'". These extensions are not allowed: php, pl, cgi, py, sh, shtml');
	}
			$url = parse_url('http://filevelocity.com/');
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"], $pauth);
			if(!preg_match('#action="([^"]+)"#', $page, $up)){
				html_error('Cannot get url action upload.', 0);
			}
			if(!preg_match("#var[\r|\n|\s]+utype='([^']+)'#", $page, $utype)){
				html_error('Cannot get user information.', 0);
			}
			if(!preg_match('#name="srv_tmp_url"[\r|\n|\s]+value="([^"]+)"#', $page, $tmp)){
				html_error('Cannot get tmp url.', 0);
			}
			$uid = uid();
			$url = parse_url($up[1].$uid.'&js_on=1&utype='.$utype[1].'&upload_type=file');
			$post["upload_type"] = "file";
            $post["sess_id"] = "";
            $post["srv_tmp_url"] = $tmp[1];
			$post['tos'] = '1';
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://filevelocity.com/", 0, $post, $lfile, $lname, "file_0");
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
			$page = geturl("filevelocity.com", 80, "/", 0, 0, $post, 0, $_GET["proxy"]);
				if(!empty($dl[1][0]))
					$download_link = 'http://filevelocity.com/'.$dl[1][0];
				else
					html_error ("Didn't find download link!");
				if(preg_match("#killcode=([^=<]+)<#", $page, $del))
					$delete_link = $download_link.'?killcode='.$del[1];
				else
					html_error ("Didn't find delete link!");
	function uid(){
				$nu = "0123456789";
				for($i=0; $i < 12; $i++){
				$rand .= $nu{mt_rand() % strlen($nu)};
				}
				return $rand;
	}
				//function by simplesdescraga 05/02/2012
function extensao($file){
				$tam = strlen($file);
				if( $file[($tam)-4] == '.' ){
				$extensao = substr($file,-3);}
				elseif( $file[($tam)-5] == '.' ){
				$extensao = substr($file,-4);}
				elseif( $file[($tam)-6] == '.' ){
				$extensao = substr($file,-5);}
				elseif( $file[($tam)-3] == '.' ){
				$extensao = substr($file,-2);
				}else{
				$extensao = NULL;}
				return $extensao;
				//function by simplesdescarga 13/01/2012
				//function fixed extensão by simplesdescarga 06/02/2012
				}
				
/**
written by simplesdescarga 06/02/2012
**/   
?>