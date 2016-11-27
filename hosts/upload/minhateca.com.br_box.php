<?php

####### Free Account Info. ###########
$minhateca_login = ""; //set your id (login)
$minhateca_pass = ""; //set your  password

##############################

$host="box.minhateca.com.br";

$not_done=true;
$continue_up=false;
if ($minhateca_login && $minhateca_pass){
		$_REQUEST['my_login'] = $minhateca_login;
		$_REQUEST['my_pass'] = $minhateca_pass;
		$_REQUEST['action'] = "FORM";
		echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
	$continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;User*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["minhateca.com.br_box"]; ?></b></small></tr>
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
<div id=login width=100% align=center>Login to <?php echo $host; ?></div>
<?php 
						$post = 
							'<?xml version="1.0" encoding="UTF-8"?>' .
							'<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">' .
							  '<s:Body>' .
							    '<Auth xmlns="http://chomikuj.pl/">' .
							      '<name>' . $_REQUEST['my_login'] . '</name>' .
							      '<passHash>' . strtolower(md5($_REQUEST['my_pass'])) . '</passHash>' .
							      '<ver>4</ver>' .
							      '<client>' .
							        '<name>chomikbox</name>' .
							        '<version>2.0.8.1</version>' .
							      '</client>' .
							    '</Auth>' .
							  '</s:Body>' .
							'</s:Envelope>';
						
						$page = geturl($host, 80, "/services/ChomikBoxService.svc", "http://".$host."/\r\nSOAPAction: http://chomikuj.pl/IChomikBoxService/Auth\r\nContent-Type: text/xml;charset=utf-8\r\nUser-Agent: Mozilla/5.0", 0, $post, 0, $_GET["uproxy"], $pauth);
						is_page($page);
						
						
						preg_match('/\<a:token\>(.*?)\<\/a:token\>/', $page, $temp);
						if($temp)
						{
							$auth_token = $temp[1];
						} else {
							html_error ('Login error');
						}
						
						preg_match('/\<a:hamsterId\>(.*?)\<\/a:hamsterId\>/', $page, $temp);
						if($temp)
						{
							$chomik_id = $temp[1];
						} else {
							html_error ('Login error');
						}

		?>
<!--<script>document.getElementById('login').style.display='none';</script>-->
<div id=info width=100% align=center>Retrive upload ID</div>
<script>document.getElementById('info').style.display='none';</script>
<?php 
						$post = 
							'<?xml version="1.0" encoding="UTF-8"?>' .
							'<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">' . 
							  '<s:Body>' .
							    '<UploadToken xmlns="http://chomikuj.pl/">' .
							      '<token>' . $auth_token . '</token>' .
							      '<folderId>0</folderId>' .
							      '<fileName>' . $lname . '</fileName>' .
							    '</UploadToken>' .
							  '</s:Body>' .
							'</s:Envelope>';

						$page = geturl($host, 80, "/services/ChomikBoxService.svc", "http://".$host."/\r\nSOAPAction: http://chomikuj.pl/IChomikBoxService/UploadToken\r\nContent-Type: text/xml;charset=utf-8\r\nUser-Agent: Mozilla/5.0", 0, $post, 0, $_GET["uproxy"], $pauth);
						is_page($page);

						preg_match('/\<a:key\>(.*?)\<\/a:key\>/', $page, $temp);
						if($temp)
						{
							$upload_key = $temp[1];
						} else {
							html_error ('Error fetching upload page');
						}
						
						preg_match('/\<a:stamp\>(.*?)\<\/a:stamp\>/', $page, $temp);
						if($temp)
						{
							$upload_time = $temp[1];
						} else {
							html_error ('Error fetching upload page');
						}
						
						preg_match('/\<a:server\>(.*?)\<\/a:server\>/', $page, $temp);
						if($temp)
						{
							$upload_server = $temp[1];
							$upload_host = explode(":", $upload_server)[0];
							$upload_port = explode(":", $upload_server)[1];
						} else {
							html_error ('Error fetching upload page');
						}

						$uploadPost['chomik_id'] = $chomik_id;
						$uploadPost['folder_id'] = '0';
						$uploadPost['key'] = $upload_key;
						$uploadPost['time'] = $upload_time;
						$uploadPost['client'] = 'MinhaBox.br-2.0.8.1';
						$uploadPost['locale'] = 'BR';
						
						$upfiles = upfile($upload_host, $upload_port, '/file/', 0, 0, $uploadPost, $lfile, $lname, "file", "", $_GET["uproxy"], $pauth, 'Mozilla/5.0');
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php   
						is_page($upfiles);					
						is_notpresent($upfiles, 'HTTP/1.1 200', 'Upload error');
						
						$pos = strpos($upfiles, '<resp res="1" fileid=');
						if($pos == false)
						{
							html_error ('Upload error');
						}
						
						preg_match('/fileid\="(.*?)"/', $upfiles, $temp);
						if($temp)
						{
							$fileid = $temp[1];
						} else {
							html_error ('Fileid not found');
						}
						
						$post = 
							'<?xml version="1.0" encoding="UTF-8"?>' .
							'<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">' .
								'<s:Body>' .
									'<Download xmlns="http://chomikuj.pl/">' .
										'<token>' . $auth_token . '</token>' .
										'<sequence>' .
											'<stamp>' . rand(0,25000) . '</stamp>' .
											'<part>0</part>' .
											'<count>1</count>' .
										'</sequence>' .
										'<disposition>download</disposition>' .
										'<list>' .
											'<DownloadReqEntry>' .
												'<id>' . $fileid . '</id>' .
											'</DownloadReqEntry>' .
										'</list>' .
									'</Download>' .
								'</s:Body>' .
							'</s:Envelope>';
						
						$page = geturl($host, 80, "/services/ChomikBoxService.svc", "http://".$host."/\r\nSOAPAction: http://chomikuj.pl/IChomikBoxService/Download\r\nContent-Type: text/xml;charset=utf-8\r\nUser-Agent: Mozilla/5.0", 0, $post, 0, $_GET["uproxy"], $pauth);
						is_page($page);
						
						$dl = array();
						preg_match('/\<globalId\>(.*?)\<\/globalId\>/', $page, $temp);
						if($temp)
						{
							$dl['globalId'] = $temp[1];
						} else {
							html_error ('Error retrive download link!');
						}
						
						preg_match_all('/\<name\>(.*?)\<\/name\>/', $page, $temp);
						if($temp)
						{
							$dl['name'] = $temp[1][1];
							$dl['filename'] = pathinfo($dl['name'], PATHINFO_FILENAME);
							$dl['extension'] = pathinfo($dl['name'], PATHINFO_EXTENSION);
						} else {
							html_error ('Error retrive download link!');
						}
						
						$download_link = 'http://minhateca.com.br'.$dl['globalId'].'/'.$dl['filename'].','.$fileid.'.'.$dl['extension'];
						
		}

// tech - Written in 26/11/2016
?>
