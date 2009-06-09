<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
            
        $ref='http://upload3.storeandserve.com/';
		$page = geturl("storeandserve.com", 80, "/", "http://storeandserve.com", 0, 0, 0, "");
        is_page($page);
		$cookies=GetCookies($page);
//		$page = geturl("upload3.storeandserve.com", 80, "/?uploads/upload&".$cookies, "http://storeandserve.com", $cookies, 0, 0, "");
//		is_page($page);
//		$action_url = '/cgi-bin/'.trim(cut_str($page,'action="cgi-bin/','"'));        

		$rnd=str_replace('.','',microtime(1));
		$Url=parse_url('http://upload3.storeandserve.com/cgi-bin/upload/ajaxupload.cgi?serial='.$rnd.'&ses_usertype=F');
?>
    <script>document.getElementById('info').style.display='none';</script>
<?php
            
//        $upfiles = @upfile("upload3.storeandserve.com", 80, $action_url, $ref."/?uploads/upload&".$cookies, $cookies, $post, $lfile, $lname, "uploadname");
        $upfiles = @upfile($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $ref."/?uploads/upload&".$cookies, $cookies, $post, $lfile, $lname, "uploadname");
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php        
        is_page($upfiles);
        unset($post);
		$post['hidSession']=cut_str($upfiles,'hidSession" value="','"');
		$post['hidFileName']=cut_str($upfiles,'hidFileName" value="','"');
		$page = geturl("upload3.storeandserve.com", 80, "/?uploads/uploaded_over_redirect", $ref, $cookies, $post);
		is_page($page);
        unset($post);
        $post['fileid_dh'] = cut_str($page,"fileid_dh' value='","'");
        $post['Message'] = cut_str($page,"Message' value='","'");
        $post['fromweb13'] = cut_str($page,"fromweb13' value='","'");
        $page = geturl("storeandserve.com", 80, "/?uploads/uploaded_links", $ref, $cookies, $post);
        is_page($page);
        $temp1 = trim(cut_str($page,"[URL=","]"));
        if (empty($temp1)) html_error("Error Get Download Link! / Ошибка получения ссылок!");
        $download_link = $temp1;
        # Upload plug'in написан Директором Зоопарка (ru-board member kamyshew). Only for Rapidget Pro!!! Rapidkill - отстой!!! Нет плагиату!!!

// sert 14.10.2008
?>