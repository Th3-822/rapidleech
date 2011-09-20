<?php
####### Default Free File Storage Provider. ###########
$Default = "0"; //Set this variable to "1" to enable the autoupload compatibility
#######################################

$not_done=true;
$continue_up=false;
if ($Default === "1"){

// To select Default Storage Providers uncomment the providers you want to be selected
// by default - you must have a MAXIMUM of 14 providers selected.

$_REQUEST['filesonic'] = "on";
$_REQUEST['depositfiles'] = "on";
$_REQUEST['wupload'] = "on";
$_REQUEST['easyshare'] = "on";
$_REQUEST['fileserve'] = "on";
//$_REQUEST['enterupload'] = "on";
$_REQUEST['megaupload'] = "on";
$_REQUEST['hotfile'] = "on";
$_REQUEST['rapidshare'] = "on";
//$_REQUEST['freakshare'] = "on";
//$_REQUEST['zshare'] = "on";
//$_REQUEST['turbobit'] = "on";
//$_REQUEST['uploadstation'] = "on";
$_REQUEST['ziddu'] = "on";
$_REQUEST['mediafire'] = "on";
//$_REQUEST['extabit'] = "on";
//$_REQUEST['bitshare'] = "on";
//$_REQUEST['filefactory'] = "on";
//$_REQUEST['megashare'] = "on";
$_REQUEST['x7_dot_to'] = "on";
$_REQUEST['ifileit'] = "on";
//$_REQUEST['gamefront'] = "on";
//$_REQUEST['badongo'] = "on";
$_REQUEST['uploadedto'] = "on";
//$_REQUEST['uploadbox'] = "on";
//$_REQUEST['ugotfile'] = "on";
//$_REQUEST['eyvx'] = "on";
//$_REQUEST['loadto'] = "on";
//$_REQUEST['oron'] = "on";
//$_REQUEST['twoshared'] = "on";
//$_REQUEST['filekeen'] = "on";
//$_REQUEST['gaiafile'] = "on";
//$_REQUEST['filehook'] = "on";
	
	$_REQUEST['action'] = "FORM";
	echo "<b><center>Use Default File Storage Provider.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<script type="text/javascript">
function maxMirrors(box)
{
    var elements = document.getElementsByTagName("input");
    var count = 0;
    for( i = 0; i < elements.length; i++ )
    {
	
        if( elements[i].type == "checkbox" )
        {
            if( elements[i].checked )
                count++;
        }
    }
    
    if( count > 14 )    
    {
        alert("Select 14 mirrors maximum!");
        box.checked = false;
	FancyForm.update(box.getParent());
        

    }
}
</script>
<table border=0 style="width:380px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />

<tr><td><label onclick="maxMirrors(this);" for="filesonic">FileSonic<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="filesonic" id="filesonic" checked="checked"></td>
<td><label onclick="maxMirrors(this);" for="depositfiles">DepositFiles<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="depositfiles" id="depositfiles" checked="checked"></td>
<td><label onclick="maxMirrors(this);" for="wupload">Wupload<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="wupload" id="wupload" checked="checked"></td></tr>
<tr><td><label onclick="maxMirrors(this);" for="easyshare">EasyShare<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="easyshare" id="easyshare" checked="checked"></td>
<td><label onclick="maxMirrors(this);" for="fileserve">FileServe<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="fileserve" id="fileserve" checked="checked"></td>
<td><label onclick="maxMirrors(this);" for="enterupload">EnterUpload<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="enterupload" id="enterupload"></td></tr>
<tr><td><label onclick="maxMirrors(this);" for="megaupload">MegaUpload<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="megaupload" id="megaupload" checked="checked"></td>
<td><label onclick="maxMirrors(this);" for="hotfile">HotFile<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="hotfile" id="hotfile" checked="checked"></td>
<td><label onclick="maxMirrors(this);" for="rapidshare">RapidShare<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="rapidshare" id="rapidshare" checked="checked"></td></tr>
<tr><td><label onclick="maxMirrors(this);" for="freakshare">FreakShare<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="freakshare" id="freakshare"></td>
<td><label onclick="maxMirrors(this);" for="zshare">ZShare<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="zshare" id="zshare"></td>
<td><label onclick="maxMirrors(this);" for="turbobit">TurboBit<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="turbobit" id="turbobit"></td></tr>
<tr><td><label onclick="maxMirrors(this);" for="uploadstation">UploadStation<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="uploadstation" id="uploadstation"></td>
<td><label onclick="maxMirrors(this);" for="ziddu">Ziddu<br/><span id="maxsize">Max. 200 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="ziddu" id="ziddu" checked="checked"></td>
<td><label onclick="maxMirrors(this);" for="mediafire">MediaFire<br/><span id="maxsize">Max. 200 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="mediafire" id="mediafire" checked="checked"></td></tr>
<tr><td><label onclick="maxMirrors(this);" for="extabit">ExtaBit<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="extabit" id="extabit"></td>
<td><label onclick="maxMirrors(this);" for="bitshare">BitShare<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="bitshare" id="bitshare"></td>
<td><label onclick="maxMirrors(this);" for="filefactory">FileFactory<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="filefactory" id="filefactory"></td></tr>
<tr><td><label onclick="maxMirrors(this);" for="megashare">MegaShare<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="megashare" id="megashare"></td>
<td><label onclick="maxMirrors(this);" for="x7_dot_to">x7.to<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="x7_dot_to" id="x7_dot_to" checked="checked"></td>
<td><label onclick="maxMirrors(this);" for="ifileit">iFileIt<br/><span id="maxsize">Max. 300 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="ifileit" id="ifileit" checked="checked"></td></tr>
<tr><td><label onclick="maxMirrors(this);" for="gamefront">GameFront<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="gamefront" id="gamefront"></td>
<td><label onclick="maxMirrors(this);" for="badongo">Badongo<br/><span id="maxsize">Max. 200 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="badongo" id="badongo"></td>
<td><label onclick="maxMirrors(this);" for="uploadedto">UploadedTo<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="uploadedto" id="uploadedto" checked="checked"></td></tr>
<tr><td><label onclick="maxMirrors(this);" for="uploadbox">UploadBox<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="uploadbox" id="uploadbox"></td>
<td><label onclick="maxMirrors(this);" for="ugotfile">uGotFile<br/><span id="maxsize">Max. 300 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="ugotfile" id="ugotfile"></td>
<td><label onclick="maxMirrors(this);" for="eyvx">Eyvx<br/><span id="maxsize">Max. 200 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="eyvx" id="eyvx"></td></tr>
<tr><td><label onclick="maxMirrors(this);" for="loadto">Loadto<br/><span id="maxsize">Max. 300 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="loadto" id="loadto"></td>
<td><label onclick="maxMirrors(this);" for="oron">Oron<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="oron" id="oron"></td>
<td><label onclick="maxMirrors(this);" for="twoshared">TwoShared<br/><span id="maxsize">Max. 100 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="twoshared" id="twoshared"></td></tr>
<tr><td><label onclick="maxMirrors(this);" for="filekeen">FileKeen<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="filekeen" id="filekeen"></td>
<td><label onclick="maxMirrors(this);" for="gaiafile">GaiaFile<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="gaiafile" id="gaiafile"></td>
<td><label onclick="maxMirrors(this);" for="filehook">FileHook<br/><span id="maxsize">Max. 400 MB</span></label><input onclick="maxMirrors(this);" type="checkbox" name="filehook" id="filehook"></td></tr>
<tr><td colspan=3 align=center><input type=submit value='Upload' /></tr>
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
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
        $rnd = time().rndNum(3);
	$page = geturl("www.mirrorcreator.com", 80, "/uber/ubr_link_upload.php", "", 0, 0, 0, "");
	
	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cookies = implode(';',$cookie);
	
	$page = geturl("www.mirrorcreator.com", 80, "/uber/ubr_link_upload.php?_=".round(microtime(true))."000", "http://www.mirrorcreator.com/", $cookies, 0, 0, "");
	is_page($page);
	$upid = "upload_id=".cut_str($page,'Upload("','"');
	$post['mail']= "";
	if(!empty($_REQUEST['filesonic'])) {$post['filesonic'] = 'on';}
	if(!empty($_REQUEST['depositfiles'])) {$post['depositfiles'] = 'on';}
	if(!empty($_REQUEST['wupload'])) {$post['wupload'] = 'on';}
	if(!empty($_REQUEST['easyshare'])) {$post['easyshare'] = 'on';}
	if(!empty($_REQUEST['fileserve'])) {$post['fileserve'] = 'on';}
	if(!empty($_REQUEST['enterupload'])) {$post['enterupload'] = 'on';}
	if(!empty($_REQUEST['megaupload'])) {$post['megaupload'] = 'on';}
	if(!empty($_REQUEST['hotfile'])) {$post['hotfile'] = 'on';}
	if(!empty($_REQUEST['rapidshare'])) {$post['rapidshare'] = 'on';}
	if(!empty($_REQUEST['freakshare'])) {$post['freakshare'] = 'on';}
	if(!empty($_REQUEST['zshare'])) {$post['zshare'] = 'on';}
	if(!empty($_REQUEST['turbobit'])) {$post['turbobit'] = 'on';}
	if(!empty($_REQUEST['uploadstation'])) {$post['uploadstation'] = 'on';}
	if(!empty($_REQUEST['ziddu'])) {$post['ziddu'] = 'on';}
	if(!empty($_REQUEST['mediafire'])) {$post['mediafire'] = 'on';}
	if(!empty($_REQUEST['extabit'])) {$post['extabit'] = 'on';}
	if(!empty($_REQUEST['bitshare'])) {$post['bitshare'] = 'on';}
	if(!empty($_REQUEST['filefactory'])) {$post['filefactory'] = 'on';}
	if(!empty($_REQUEST['megashare'])) {$post['megashare'] = 'on';}
	if(!empty($_REQUEST['x7_dot_to'])) {$post['x7_dot_to'] = 'on';}
	if(!empty($_REQUEST['ifileit'])) {$post['ifileit'] = 'on';}
	if(!empty($_REQUEST['gamefront'])) {$post['gamefront'] = 'on';}
	if(!empty($_REQUEST['badongo'])) {$post['badongo'] = 'on';}
	if(!empty($_REQUEST['uploadedto'])) {$post['uploadedto'] = 'on';}
	if(!empty($_REQUEST['uploadbox'])) {$post['uploadbox'] = 'on';}
	if(!empty($_REQUEST['ugotfile'])) {$post['ugotfile'] = 'on';}
	if(!empty($_REQUEST['eyvx'])) {$post['eyvx'] = 'on';}
	if(!empty($_REQUEST['loadto'])) {$post['loadto'] = 'on';}
	if(!empty($_REQUEST['oron'])) {$post['oron'] = 'on';}
	if(!empty($_REQUEST['twoshared'])) {$post['twoshared'] = 'on';}
	if(!empty($_REQUEST['filekeen'])) {$post['filekeen'] = 'on';}
	if(!empty($_REQUEST['gaiafile'])) {$post['gaiafile'] = 'on';}
	if(!empty($_REQUEST['filehook'])) {$post['filehook'] = 'on';}

	$url=parse_url('http://www.mirrorcreator.com/cgi-bin/ubr_upload.pl?'.$upid);	
?>
<script>document.getElementById('info').style.display='none';</script>
<?php

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://www.mirrorcreator.com/', $cookies, $post, $lfile, $lname, "upfile_$rnd");
		
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
	$page = geturl("www.mirrorcreator.com", 80, "/process.php?$upid", "http://www.mirrorcreator.com/process.php?$upid", $cookies, 0, 0, "");
        preg_match('%<a href="(.*)" target="_blank">%',$page,$match);
        $download_link= trim($match[1]);
}
function rndNum($lg){
$str="0123456789"; 
for ($i=1;$i<=$lg;$i++){
$st=rand(1,9);
$pnt.=substr($str,$st,1);
}
return $pnt;
}	
// Made by Baking 07/02/2010 00:02
//Fix by VinhNhaTrang 15/12/2010
// Updated by CountVirgo, fixed issues, added new providers - 13/09/2011
?>