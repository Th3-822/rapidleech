<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php 
            $page = geturl("megaupload.com", 80, "/", "", 0, 0, 0, "");
            is_page($page);
            preg_match('/UPLOAD_IDENTIFIER=(.*?)"/i', $page, $id);
            preg_match('/<form.*action="(.*?)".*id="uploadform"/i', $page, $uplink);
            $action_url = $uplink[1];
            $url = parse_url($action_url);
            $post['message'] = $lname;
            $post['UPLOAD_IDENTIFIER'] = $id[1];
            $post['sessionid'] = $id[1];
            $post['accept'] = 'on';
?>
<script>document.getElementById('info').style.display='none';</script>

<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php 
   $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://www.megaupload.com/', 0, $post, $lfile, $lname, "file");
            is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
            preg_match('/downloadurl *= *\'(.*?)\'/i', $upfiles, $dllink);
            $download_link = $dllink[1];
?>