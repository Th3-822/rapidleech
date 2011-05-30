<?
####### Default Free File Storage Provider. ###########
$Default = "0"; //Set this variable to "1" to enable the autoupload compatibility
#######################################

$not_done=true;
$continue_up=false;
if ($Default === "1"){

//To select Default Storage Provider uncomment  the provider you want to be selected
//be default but you MUST comment another storage provider
// and finally you MUST have ONLY 8 provider selected !!

//	$_REQUEST['megaupload'] = "1";
//	$_REQUEST['flyupload'] = "1";
//	$_REQUEST['sendspace'] = "1";
//	$_REQUEST['sharedzilla'] = "1";
//	$_REQUEST['netload'] = "1";
//	$_REQUEST['loadto'] = "1";
//	$_REQUEST['_2shared'] = "1";
//	$_REQUEST['rapidshare'] = "1";
	$_REQUEST['hotfile'] = "1";
	$_REQUEST['easyshare'] = "1";
	$_REQUEST['zshare'] = "1";
	$_REQUEST['badongo'] = "1";
	$_REQUEST['megashare'] = "1";
	$_REQUEST['zippyshare'] = "1";
	$_REQUEST['filefactory'] = "1";
	$_REQUEST['depositfiles'] = "1";
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
    
    if( count > 8 )    
    {
        alert("Select 8 mirrors only!");
        box.checked = false;
	FancyForm.update(box.getParent());
        

    }
}
</script>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td><input  onclick="maxMirrors(this);" type="checkbox" name="depositfiles" id="depositfiles"  checked="checked" />DepositFiles</label></td>
<td><label><input  onclick="maxMirrors(this);" type="checkbox" name="hotfile" id="hotfile"  checked="checked" />HotFile</label></td>
<tr><td><label><input  onclick="maxMirrors(this);" type="checkbox" name="megaupload" id="megaupload"   />MegaUpload</label></td>
<td><label><input  onclick="maxMirrors(this);" type="checkbox" name="easyshare" id="easyshare"  checked="checked" />EasyShare</label></td>
<tr><td><label><input  onclick="maxMirrors(this);" type="checkbox" name="zshare" id="zshare"  checked="checked" />ZShare</label></td>
<td><label><input  onclick="maxMirrors(this);" type="checkbox" name="flyupload" id="flyupload"   />FlyUpload</label></td>
<tr><td><label><input  onclick="maxMirrors(this);" type="checkbox" name="sendspace" id="sendspace"   />SendSpace</label></td>
<td><input  onclick="maxMirrors(this);" type="checkbox" name="sharedzilla" id="sharedzilla"   />SharedZilla</label></td>
<tr><td><label><input  onclick="maxMirrors(this);" type="checkbox" name="badongo" id="badongo"  checked="checked" />Badongo</label></td>
<td><label><input  onclick="maxMirrors(this);" type="checkbox" name="netload" id="netload"   />NetLoad</label></td>
<tr><td><label><input  onclick="maxMirrors(this);" type="checkbox" name="loadto" id="loadto"   />Loadto</label></td>
<td><label><input  onclick="maxMirrors(this);" type="checkbox" name="_2shared" id="_2shared"   />_2Shared</label></td>
<tr><td><label><input  onclick="maxMirrors(this);" type="checkbox" name="megashare" id="megashare"  checked="checked" />MegaShare</label></td>
<td><label><input  onclick="maxMirrors(this);" type="checkbox" name="rapidshare" id="rapidshare"   />RapidShare</label></td>
<tr><td><label><input  onclick="maxMirrors(this);" type="checkbox" name="zippyshare" id="zippyshare"  checked="checked" />ZippyShare</label></td>
<td><label><input  onclick="maxMirrors(this);" type="checkbox" name="filefactory" id="filefactory"  checked="checked" />FileFactory</label></td>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
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

	$rand = mt_rand(1000000000000, 1999999999999);
	$page1 = geturl("www.uploadmirrors.com", 80, "/", "", 0, 0, 0, "");
	
	preg_match_all('/Set-Cookie: (.*);/U',$page,$temp);
	$cookie = $temp[1];
	$cookies = implode(';',$cookie);
	
	echo $cookies;
	
	$page = geturl("uploadmirrors.com", 80, "/ubr_link_upload.php?rnd_id=$rand", "http://uploadmirrors.com/", $cookies, 0, 0, "");
	is_page($page);
	$upid = "upload_id=".cut_str($page,'Upload("','"');
	
	
	$post['mail']= "";
	
	if(!empty($_REQUEST['hotfile'])) {$post['hotfile'] = 'on';}
	if(!empty($_REQUEST['depositfiles'])) {$post['depositfiles'] = 'on';}
	if(!empty($_REQUEST['megaupload'])) {$post['megaupload'] = 'on';}
	if(!empty($_REQUEST['easyshare'])) {$post['easyshare'] = 'on';}
	if(!empty($_REQUEST['zshare'])) {$post['zshare'] = 'on';}
	if(!empty($_REQUEST['flyupload'])) {$post['flyupload'] = 'on';}
	if(!empty($_REQUEST['sendspace'])) {$post['sendspace'] = 'on';}
	if(!empty($_REQUEST['sharedzilla'])) {$post['sharedzilla'] = 'on';}
	if(!empty($_REQUEST['badongo'])) {$post['badongo'] = 'on';}
	if(!empty($_REQUEST['netload'])) {$post['netload'] = 'on';}
	if(!empty($_REQUEST['loadto'])) {$post['loadto'] = 'on';}
	if(!empty($_REQUEST['_2shared'])) {$post['_2shared'] = 'on';}
	if(!empty($_REQUEST['megashare'])) {$post['megashare'] = 'on';}
	if(!empty($_REQUEST['rapidshare'])) {$post['rapidshare'] = 'on';}
	if(!empty($_REQUEST['zippyshare'])) {$post['zippyshare'] = 'on';}
	if(!empty($_REQUEST['filefactory'])) {$post['filefactory'] = 'on';}
	$url=parse_url('http://uploadmirrors.com'.'/cgi-bin/ubr_upload.pl?'.$upid);
	
?>
<script>document.getElementById('info').style.display='none';</script>
<?

	$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),'http://uploadmirrors.com/', $cookies, $post, $lfile, $lname, "upfile_0");
	
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?
	$page = geturl("uploadmirrors.com", 80, "/process.php?$upid", "", 0, 0, 0, "");
	
	sleep(2);
	
	$ddl=cut_str($page,'[code]','[/code]');
	
	$download_link= $ddl;
	
}	
// Made by Baking 20/08/2009 19:46
?>