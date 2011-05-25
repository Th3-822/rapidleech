<?php
####### Default Free File Storage Provider. ###########
$Default = "0"; //Set this variable to "1" to enable the autoupload compatibility
#######################################

$not_done=true;
$continue_up=false;
if ($Default === "1"){

//To select Default Storage Provider uncomment  the provider you want to be selected
//be default but you MUST comment another storage provider
// and finally you MUST have ONLY 8 provider selected !!

//$_REQUEST['rapidshare'] = "on";
//$_REQUEST['zshare'] = "on";
$_REQUEST['hotfile'] = "on";
$_REQUEST['megaupload'] = "on";
//$_REQUEST['megashare'] = "on";
//$_REQUEST['twoshared'] = "on";
$_REQUEST['depositfiles'] = "on";
//$_REQUEST['sendspace'] = "on";
//$_REQUEST['filefactory'] = "on";
//$_REQUEST['hotfile'] = "on";
//$_REQUEST['mediafire'] = "on";
// $_REQUEST['freakshare'] = "on";
// $_REQUEST['ifileit'] = "on";
// $_REQUEST['badongo'] = "on";
// $_REQUEST['uploadbox'] = "on";
// $_REQUEST['filefront'] = "on";
// $_REQUEST['storageto'] = "on";
// $_REQUEST['uploading'] = "on";
// $_REQUEST['zippyshare'] = "on";
// $_REQUEST['netload'] = "on";
$_REQUEST['easyshare'] = "on";
// $_REQUEST['ziddu'] = "on";
// $_REQUEST['loadto'] = "on";
// $_REQUEST['sharebaseto'] = "on";
	
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
    
    if( count > 10 )    
    {
        alert("Select 10 mirrors only!");
        box.checked = false;
	FancyForm.update(box.getParent());
        

    }
}
</script>
<table border=0 style="width:380px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' /> 

<tr><td><input  onclick="maxMirrors(this);" type="checkbox" name="rapidshare" id="rapidshare"   />RapidShare</td>
<td><input  onclick="maxMirrors(this);" type="checkbox" name="zshare" id="zshare"   />ZShare</td>
<td><input  onclick="maxMirrors(this);" type="checkbox" name="hotfile" id="uploadedto"   />uploaded.to</td></tr>

<tr><td><input  onclick="maxMirrors(this);" type="checkbox" name="megaupload" id="megaupload" checked="checked"/>MegaUpload
<td><input  onclick="maxMirrors(this);" type="checkbox" name="megashare" id="megashare"   />MegaShare
<td><input  onclick="maxMirrors(this);" type="checkbox" name="twoshared" id="twoshared"/>twoshared</td>

<tr><td><input  onclick="maxMirrors(this);" type="checkbox" name="depositfiles" id="depositfiles" checked="checked"  />DepositFiles
<td><input  onclick="maxMirrors(this);" type="checkbox" name="sendspace" id="sendspace"/>SendSpace
<td><input  onclick="maxMirrors(this);" type="checkbox" name="filefactory" id="filefactory"   />FileFactory</td>

<tr><td><input  onclick="maxMirrors(this);" type="checkbox" name="hotfile" id="hotfile" checked="checked"  />HotFile
<td><input  onclick="maxMirrors(this);" type="checkbox" name="mediafire" id="mediafire"   />MediaFire
<td><input  onclick="maxMirrors(this);" type="checkbox" name="freakshare" id="freakshare"   />FreakShare</td>

<tr><td><input  onclick="maxMirrors(this);" type="checkbox" name="ifileit" id="ifileit"  />ifileit
<td><input  onclick="maxMirrors(this);" type="checkbox" name="badongo" id="badongo"   />Badongo
<td><input  onclick="maxMirrors(this);" type="checkbox" name="uploadbox" id="uploadbox"   />uploadbox</td>

<tr><td><input  onclick="maxMirrors(this);" type="checkbox" name="filefront" id="filefront"  />filefront
<td><input  onclick="maxMirrors(this);" type="checkbox" name="storageto" id="storageto"  />storageto
<td><input  onclick="maxMirrors(this);" type="checkbox" name="uploading" id="uploading"  />uploading</td>

<tr><td><input  onclick="maxMirrors(this);" type="checkbox" name="zippyshare" id="zippyshare"   />ZippyShare
<td><input  onclick="maxMirrors(this);" type="checkbox" name="netload" id="netload"/>NetLoad
<td><input  onclick="maxMirrors(this);" type="checkbox" name="easyshare" id="easyshare" checked="checked" />EasyShare</td>

<tr><td><input  onclick="maxMirrors(this);" type="checkbox" name="ziddu" id="ziddu"/>ziddu
<td><input  onclick="maxMirrors(this);" type="checkbox" name="loadto" id="loadto"/>Loadto
<td><input  onclick="maxMirrors(this);" type="checkbox" name="sharebaseto" id="sharebaseto"/>sharebaseto</td>
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
	if(!empty($_REQUEST['hotfile'])) {$post['hotfile'] = 'on';}
	if(!empty($_REQUEST['megaupload'])) {$post['megaupload'] = 'on';}
	if(!empty($_REQUEST['depositfiles'])) {$post['depositfiles'] = 'on';}
	if(!empty($_REQUEST['easyshare'])) {$post['easyshare'] = 'on';}
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
?>