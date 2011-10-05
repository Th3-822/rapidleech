<?php
$Url2=parse_url('http://www.filejungle.com/upload.php');
$page2 = geturl($Url2["host"], $Url2["port"] ? $Url2["port"] : 80, $Url2["path"] . ($Url2["query"] ? "?" . $Url2["query"] : ""), 0, 0, 0, 0, $_GET["proxy"], $pauth);
preg_match('#var uploadUrl = \'(.+)\';#',$page2,$uplink);
?>
<script>document.getElementById('info').style.display='none';</script>
<div id="info" align="center">Uploading..</div>
<?php
$host=parse_url($uplink[1]);
$fileSize = getSize($lfile);
$filecontent = file_get_contents($lfile);
$zapros="PUT $uplink[1] HTTP/1.1
Host: u.filejungle.com
Connection: keep-alive
Content-Length: $fileSize
Origin: http://www.filejungle.com
X-File-Size: $fileSize
X-File-Name: $lname
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.186 Safari/535.1
Content-Type: multipart/form-data
Accept: */*
Referer: http://www.filejungle.com/upload.php
Accept-Encoding: gzip,deflate,sdch
Accept-Language: zh-TW,zh;q=0.8,en-US;q=0.6,en;q=0.4
Accept-Charset: Big5,utf-8;q=0.7,*;q=0.3

$filecontent
";
$port=($host['port'])?$host['port']:80;
$fp = fsockopen ($host['host'], $port, &$errno, &$errstr, 30);
if(!$fp){
     echo "$errstr ($errno)\n";
}else{
    fputs ($fp, $zapros);
    while (!feof($fp)) {
        $upfiles.= fgets ($fp,128);
    }
    fclose ($fp);
}
preg_match('#shortenCode":"(.+)"}#',$upfiles,$ddl);
preg_match('#deleteCode":"(.+)","fileName"#',$upfiles,$del);
if (!empty($ddl[1]))
  $download_link = 'http://www.filejungle.com/f/' . $ddl[1] . '/' . $lname;
else
  html_error ('Didn\'t find downloadlink!');
if (!empty($del[1]))
  $delete_link= 'http://www.filejungle.com/f/' . $ddl[1] . '/delete/' . $del[1];
else
  html_error ('Didn\'t find deletelink!');
?>
</td></tr></table>