<table width="100%" align="center">
	<tr>
		<td align=center>
<?php
	$url = parse_url('http://upload.easy-share.com/accounts/upload_backend/perform/ajax');
	$post['Filename'] = $lname;
	$post['Upload'] = 'Submit Query';
	
	$upagent = "Shockwave Flash";
	$upfiles = upfile ( $url ["host"], $url ["port"] ? $url ["port"] : 80, $url ["path"] . ($url ["query"] ? "?" . $url ["query"] : ""), 0, 0, $post, $lfile, $lname, "Filedata", "", 0, 0, $upagent );
	
	?>
	<script>document.getElementById('progressblock').style.display='none';</script>
	<?php
		
	preg_match('/<input type="text" size="50" value="(.*)" \/>/',$upfiles,$tmp);
	$download_link = $tmp[1];
	preg_match('/<a class="del" href="javascript:;">(.*)<\/a>/',$upfiles,$tmp);
	$delete_link = $tmp[1];
	
/**************************************************\  
Fixed by Raj Malhotra 16 May 2010 => Fixed Couldn't connect to Shockwave Flash at port 80
\**************************************************/
?>