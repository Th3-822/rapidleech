<table width=600 align=center>
</td></tr>
<tr><td align=center>
	
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
			$Href = "http://www.yousendit.com/ajaxgateway.php?action=GetBatchId&bname=".$lname."&fcount=1&pnref=&cc_four=&cc_exp=&cc_id=&amount=0&lock=0&desc=&dropbox=&upl_subject=&exp_interval=7%20DAY&r=".rand(10000, 999999999);
			$Url = parse_url($Href);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match('/batchid":"(.*)"}/i', $page, $bid);
			
			$Href = "http://www.yousendit.com/ajaxgateway.php?action=GetFtf&bid=".$bid[1]."&r=".rand(10000, 999999999);
			$Url = parse_url($Href);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
			is_page($page);
			preg_match_all('/Set-Cookie: (.*);/i', $page, $cook);
			$cookie = implode(";", $cook[0]);
			
			preg_match('/"url":"(.*)","authid":"(.*)"}/i', $page, $auid);
			$url_action = 'http://'.$auid[1].'/upload/'.$auid[2];		

			$post["rcpt"]= 'qqq@qqq.com';
			$post["pdf_mark"]= 'false';
			$post["bid"]= $bid[1];
			$post["rurl"]= 'http://www.yousendit.com/transfer.php?action=send_notification&fname='.$lname.'&batch_id='.$bid[1];
			$post["eurl"]= 'http://www.yousendit.com/transfer.php?myaccount=true';
			$post["pstate"]= 'pstate';
			$post["expiration_time"]= '7 DAY';
			$post["download_days"]= '100';
?>
<script>document.getElementById('info').style.display='none';</script>
<?php
			$url = parse_url($url_action);
			$upagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1";
			$upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://www.yousendit.com/", 0, $post, $lfile, $lname, "fname");
			
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php		
			is_page($upfiles);
			is_notpresent($upfiles,'batch_id','File not upload');
			$download_link = 'http://www.yousendit.com/download/'.$bid[1];
?>