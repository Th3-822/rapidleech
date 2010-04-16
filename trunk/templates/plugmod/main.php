<?php
if (!defined('RAPIDLEECH')) {
	require_once("index.html");
	exit;
}
?>
<table align="center">
<tbody>
<tr>
<td valign="top">
<table width="100%"  border="0">
<tr>
<td valign="top">
<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="131" height="100%">
<div class="cell-plugin"><?php echo lang(333); ?></div>
</td>
</tr>
<tr>
<td>
<div align="center" class="plugincolhd"><b><small><?php echo count($host); ?></small></b> <?php echo lang(333); ?></div></td>
</tr>
<tr>
<td height="100%" style="padding:3px;">
<div dir="rtl" align="left" style="overflow-y:scroll; height:150px; padding-left:5px;">
<?php
ksort($host);
foreach ($host as $site => $file)
	{
	echo '<span class="plugincollst">'.$site.'</span><br />';
	}
?>
</div>
<br />

<?php
global $premium_acc, $mu_cookie_user_value;
if ( !empty ( $premium_acc ) || ( $mu_cookie_user_value ) )
{
?>
<div class="cell-plugin"><?php echo lang(376); ?></div>
<table border="0">
	<tr>
		<td height="100%" style="padding:3px;">
			<div dir="rtl" align="left" style="padding-left:5px;">
<?php
			if ( !empty ( $premium_acc ) )
			{
				foreach ( $premium_acc as $serverName => $value )
				{
					echo '<span class="plugincollst">'. str_replace( '_', '.', $serverName ) .'</span><br />';
				}
			}
			
			if ( $mu_cookie_user_value )
			{
				echo '<span class="plugincollst">Megaupload</span><br />';
			}
?>
			</div>
		</td>
	</tr>
</table>
<br />
<?php
}
?>

<input class="button-auto" type="button" value="<?php echo lang(334); ?>" onclick="window.open('audl.php');return false;" />
<br />
<input class="button-auto" type="button" value="<?php echo lang(335); ?>" onclick="window.open('auul.php');return false;" />
<br />
[ <a href="javascript:openNotes();"><?php echo lang(327); ?>.txt</a> ]
</td>
</tr>
</table>
</td>
</tr>
</table></td>
<td align="center" valign="top"><table border="0" cellpadding="0" cellspacing="1">
<tbody>
<tr>
<td id="navcell1" class="cell-nav" onclick="javascript:switchCell(1);"><?php echo lang(329); ?></td>
<td id="navcell2" class="cell-nav" onclick="javascript:switchCell(2);"><?php echo lang(330); ?></td>
<td id="navcell3" class="cell-nav" onclick="javascript:switchCell(3);"><?php echo lang(331); ?></td>
<td id="navcell4" class="cell-nav" onclick="javascript:switchCell(4);"><?php echo lang(332); ?></td>
</tr>
</tbody>
</table>
<table id="tb_content">
<tbody>
<tr>
<td align="center">
<form action="<?php echo $PHP_SELF; ?>" name="transload" method="post"<?php if ($options['new_window']) { echo ' target="_blank"'; } ?>>
<table class="tab-content" id="tb1" cellspacing="5" width="100%">
<tbody>
<tr>
<td align="left">
<b><?php echo lang(207); ?>:</b><br />&nbsp;<input type="text" name="link" id="link" size="50" /><br /><br />
<b><?php echo lang(208); ?>:</b><br />&nbsp;<input type="text" name="referer" id="referer" size="50" />
</td>
<td align="center">
<input value="<?php echo lang(209); ?>" type="<?php echo ($options['new_window'] && $options['new_window_js']) ? 'button" onclick="new_transload_window();' : 'submit'; ?>" />
</td>
</tr>
<tr>
<td align="left"><input type="checkbox" name="user_pass" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('usernpass').style.display=displ;" value="on" />&nbsp;<?php echo lang(210); ?></td>
</tr>
<tr id="usernpass" style="display: none;">
<td align="center">
<?php echo lang(211); ?>: <input type="text" name="iuser" value="" /><br />
<?php echo lang(212); ?>: <input type="text" name="ipass" value="" />
</td>
</tr>
<tr>
<td align="left"><input type="checkbox" name="add_comment" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('comment').style.display=displ;" />&nbsp;<?php echo lang(213); ?></td>
</tr>
<tr id="comment" style="display: none;">
<td>
<textarea name="comment" rows="4" cols="50"></textarea>
</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
<td align="left">
<small style="color:#55bbff"><?php echo lang(214); ?>:</small><hr />
<label><input type="checkbox" name="dis_plug" />&nbsp;<small><?php echo lang(215); ?></small></label>
</td>
</tr>
<tr>
<td align="left">
<label><input type="checkbox" name="ytube_mp4" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('ytubeopt').style.display=displ;"<?php echo isset($_POST['yt_fmt']) ? ' checked="checked"' : ''; ?> />&nbsp;<small><?php echo lang(216); ?></small></label>
<table width="150" border="0" id="ytubeopt" style="display: none;">
<tr>
<td colspan="2" style="white-space: nowrap;"><input type="checkbox" name="ytdirect" /><small>&nbsp;<?php echo lang(217); ?></small></td>
</tr>
<tr>
<td align="left"><small><?php echo lang(218); ?></small></td>
<td align="left">
<select name="yt_fmt" id="yt_fmt">
<option value="highest" selected="selected"><?php echo lang(219); ?></option>
<option value="0"><?php echo lang(220); ?></option>
<option value="5"><?php echo lang(221); ?></option>
<option value="6"><?php echo lang(222); ?></option>
<option value="13"><?php echo lang(223); ?></option>
<option value="17"><?php echo lang(224); ?></option>
<option value="18"><?php echo lang(225); ?></option>
<option value="22"><?php echo lang(226); ?></option>
<option value="34"><?php echo lang(227); ?></option>
<option value="35"><?php echo lang(228); ?></option>
<option value="37"><?php echo lang(377); ?></option>

</select>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td align="left"><label><input type="checkbox" name="imageshack_tor" id="imageshack_tor" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('torpremiumblock').style.display=displ;" <?php if (is_array($imageshack_acc)) print ' checked="checked"'; ?> />&nbsp;<small><?php echo lang(229); ?></small></label><table width="150" border="0" id="torpremiumblock" style="display: none;">
<tr><td><?php echo lang(230); ?>:&nbsp;</td><td><input type="text" name="tor_user" id="tor_user" size="15" value="" /></td></tr>
<tr><td><?php echo lang(231); ?>:&nbsp;</td><td><input type="password" name="tor_pass" id="tor_pass" size="15" value="" /></td></tr>
</table>
</td>
</tr>
<tr>
<td align="left">
<label><input type="checkbox" name="mu_acc" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('mupremiumblock').style.display=displ;" <?php if ($mu_cookie_user_value) print ' checked="checked"'; ?> />&nbsp;<small><?php echo lang(232); ?></small></label>
<table width="150" border="0" id="mupremiumblock" style="display: none;">
<tr><td><?php echo lang(233); ?>=</td><td><input type="text" name="mu_cookie" id="mu_cookie" size="25" value="" /></td></tr>
</table>
</td>
</tr>
<tr>
<td align="left">
<label><input type="checkbox" name="vBulletin_plug" />&nbsp;<small><?php echo lang(234); ?></small></label>
</td>
</tr>
<tr>
<td align="left">
<label><input type="checkbox" name="cookieuse" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('cookieblock').style.display=displ;" />&nbsp;<small><?php echo lang(235); ?></small></label>
<table width="150" border="0" id="cookieblock" style="display: none;">
<tr><td><?php echo lang(236); ?>;</td><td><input type="text" name="cookie" id="cookie" size="25" value="" /></td></tr>
</table>
</td>
</tr>
</tbody>
</table>
<table class="hide-table" id="tb2" cellspacing="5" width="100%">
<tbody>
<tr>
<td align="center">
<table align="center" style="text-align: justify;">
<tr>
<td><input type="checkbox" name="domail" id="domail" onclick="document.getElementById('emailtd').style.display=document.getElementById('splittd').style.display=this.checked?'':'none';document.getElementById('methodtd').style.display=(document.getElementById('splitchkbox').checked ? (this.checked ? '' : 'none') : 'none');"<?php echo $_COOKIE["domail"] ? ' checked="checked"' : ''; ?> />&nbsp;<?php echo lang(237); ?></td>
<td>&nbsp;</td>
<td id="emailtd"<?php echo $_COOKIE["domail"] ? '' : ' style="display: none;"'; ?>><?php echo lang(238); ?>:&nbsp;<input type="text" name="email" id="email"<?php echo $_COOKIE["email"] ? ' value="'.$_COOKIE["email"].'"' : ''; ?> /></td>
</tr>
<tr>
<td></td>
</tr>
<tr id="splittd"<?php echo $_COOKIE["split"] ? '' : ' style="display: none;"'; ?>>
<td>
<input id="splitchkbox" type="checkbox" name="split" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('methodtd').style.display=displ;"<?php echo $_COOKIE["split"] ? ' checked="checked"' : ''; ?> />&nbsp;<?php echo lang(239); ?>
</td>
<td>&nbsp;</td>
<td id="methodtd"<?php echo $_COOKIE["split"] ? '' : ' style="display: none;"'; ?>>
<table>
<tr>
<td><?php echo lang(240); ?>:&nbsp;<select name="method"><option value="tc"<?php echo $_COOKIE["method"] == "tc" ? " selected" : ""; ?>><?php echo lang(241); ?></option><option value="rfc"<?php echo $_COOKIE["method"] == "rfc" ? ' selected="selected"' : ''; ?>><?php echo lang(242); ?></option></select></td>
</tr>
<tr>
<td><?php echo lang(243); ?>:&nbsp;<input type="text" name="partSize" size="2" value="<?php echo $_COOKIE["partSize"] ? $_COOKIE["partSize"] : 10; ?>" />&nbsp;<?php echo lang(244); ?></td>
</tr>
</table>
</td>
</tr>
<tr>
<td><input type="checkbox" id="useproxy" name="useproxy" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('proxy').style.display=displ;"<?php echo $_COOKIE["useproxy"] ? ' checked="checked"' : ''; ?> />&nbsp;<?php echo lang(245); ?></td>
<td>&nbsp;</td>
<td id="proxy"<?php echo $_COOKIE["useproxy"] ? '' : ' style="display: none;"'; ?>>
<table width="150" border="0">
<tr><td><?php echo lang(246); ?>:&nbsp;</td><td><input type="text" name="proxy" id="proxyproxy" size="20"<?php echo $_COOKIE["proxy"] ? ' value="'.$_COOKIE["proxy"].'"' : ''; ?> /></td></tr>
<tr><td><?php echo lang(247); ?>:&nbsp;</td><td><input type="text" name="proxyuser" id="proxyuser" size="20"<?php echo $_COOKIE["proxyuser"] ? ' value="'.$_COOKIE["proxyuser"].'"' : ''; ?> /></td></tr>
<tr><td><?php echo lang(248); ?>:&nbsp;</td><td><input type="text" name="proxypass" id="proxypass" size="20"<?php echo $_COOKIE["proxypass"] ? ' value="'.$_COOKIE["proxypass"].'"' : ''; ?> /></td></tr>
</table>
</td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td><input type="checkbox" name="premium_acc" id="premium_acc" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('premiumblock').style.display=displ;"<?php if (count($premium_acc) > 0) print ' checked="checked"'; ?> />&nbsp;<?php echo lang(249); ?></td>
<td>&nbsp;</td>
<td id="premiumblock" style="display: none;">
<table width="150" border="0">
<tr><td><?php echo lang(250); ?>:&nbsp;</td><td><input type="text" name="premium_user" id="premium_user" size="15" value="" /></td></tr>
<tr><td><?php echo lang(251); ?>:&nbsp;</td><td><input type="password" name="premium_pass" id="premium_pass" size="15" value="" /></td></tr>
</table>
</td>
</tr>
<tr>
<td></td>
</tr>
<tr<?php echo (!$options['download_dir_is_changeable'] ? ' style="display:none;"' : '');?>>
<td><input type="checkbox" name="saveto" id="saveto" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('path').style.display=displ;"<?php echo $_COOKIE["saveto"] ? ' checked="checked"' : ''; ?> />&nbsp;<?php echo lang(252); ?></td>
<td>&nbsp;</td>
<td id="path"<?php echo $_COOKIE["saveto"] ? '' : ' style="display: none;"'; ?>><?php echo lang(253); ?>:&nbsp;<input type="text" name="path" size="40" value="<?php echo ($_COOKIE["path"] ? $_COOKIE["path"] : (substr($options['download_dir'], 0, 6) != "ftp://" ? realpath(DOWNLOAD_DIR) : $options['download_dir'])); ?>" /></td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td><input type="checkbox" name="savesettings" id="savesettings"<?php echo $_COOKIE["savesettings"] ? ' checked="checked"' : ''; ?> onclick="javascript:var displ=this.checked?'':'none';document.getElementById('clearsettings').style.display=displ;" />&nbsp;<?php echo lang(254); ?></td>
<td>&nbsp;</td>
<td id="clearsettings"<?php echo $_COOKIE["savesettings"] ? '' : ' style="display: none;"'; ?>><a href="javascript:clearSettings();"><?php echo lang(255); ?></a></td>
</tr>
</table>
</td>
</tr>
</tbody>
</table>
</form>
<table class="hide-table" id="tb3" cellspacing="5" width="100%">
<tbody><tr><td align="center" width="100%">
<?php
_create_list();
require_once(CLASS_DIR."options.php");
if($list)
  {
  if ($options['show_all'] === true)
    {
    unset($Path);
    }
  ?>
<a href="javascript:setCheckboxes(1);" class="chkmenu"><?php echo lang(256); ?></a> |
<a href="javascript:setCheckboxes(0);" class="chkmenu"><?php echo lang(257); ?></a> |
<a href="javascript:setCheckboxes(2);" class="chkmenu"><?php echo lang(258); ?></a> |
<a href="#" onclick="$('#flist_match_hitems').toggle();$('#flist_match_search').focus();return false;" class="chkmenu"><?php echo lang(384); ?></a>
<?php
  if ($options['show_all'] === true) {
?>
| <a href="javascript:showAll();"><?php echo lang(259); ?>&#173;
<script type="text/javascript">
if(getCookie("showAll") == 1)
  {
  document.write("<?php echo lang(260); ?>");
  }
else
  {
  document.write("<?php echo lang(261); ?>");
  }
</script></a>
<?php
  }
?>
<span id="flist_match_hitems" style="display:none;padding-top: 20px;"><br /><br />
<input type="text" size="20" id="flist_match_search" onkeypress="javascript:if(event.keyCode==13){flist_match();}" />
<input type="button" value="<?php echo lang(385); ?>" onclick="flist_match();" /><input type="checkbox" id="flist_match_ins" checked="checked" /><?php echo lang(386); ?>
</span>
<br /><br />
<form action="<?php echo $PHP_SELF; ?>" name="flist" method="post">
<?php echo renderActions(); ?>
<div style="position:relative; width:800px; padding:1px 0px 1px 0px;">
<div style="overflow:auto; height:auto; max-height:450px; width: 800px;">

<table id="table_filelist_h" cellpadding="3" cellspacing="1" class="filelist" align="left" style="position:absolute;left:0px;top:0px;">
<tbody>
<tr class="flisttblhdr" valign="bottom">
<td id="file_list_checkbox_title_h">
&nbsp;
</td>
<td>
<b><?php echo lang(262); ?></b></td>
<td><b><?php echo lang(263); ?></b></td>
<td><b><?php echo lang(264); ?></b></td>
<td><b><?php echo lang(265); ?></b></td>
</tr>
</tbody>
</table>
<table id="table_filelist" cellpadding="3" cellspacing="1" width="100%" class="filelist" align="left">
<thead>
<tr class="flisttblhdr" valign="bottom">
<td id="file_list_checkbox_title" class="sorttable_checkbox">
&nbsp;
</td>
<td class="sorttable_alpha">
<b><?php echo lang(262); ?></b></td>
<td><b><?php echo lang(263); ?></b></td>
<td><b><?php echo lang(264); ?></b></td>
<td><b><?php echo lang(265); ?></b></td>
</tr>
</thead>
<tbody>
<?php
  }
else
  {
  echo "<center>".lang(266)."</center>";
  if ($options['show_all'] === true)
    {
    unset($Path);
    ?>
<a href="javascript:showAll();"><?php echo lang(259); ?>&#173;
<script type="text/javascript">
if(getCookie("showAll") == 1)
  {
  document.write("<?php echo lang(260); ?>");
  }
else
  {
  document.write("<?php echo lang(261); ?>");
  }
</script></a><br /><br />
<?php
    }
  }
if($list)
  {
  $total_files = 0;
  $filecount = 0;
  foreach($list as $key => $file)
    {
    if (($size_time = file_data_size_time($file["name"])) === false) { continue; }
    $total_files++;
    $total_size+=$size_time[0];
?>
<tr class="flistmouseoff" onmouseover="this.className='flistmouseon'" onmouseout="this.className='flistmouseoff'" align="center" title="<?php echo htmlentities($file["name"]); ?>" onmousedown="checkFile(<?php echo $filecount; ?>); return false;">
<td><input onmousedown="checkFile(<?php echo $filecount;?>); return false;" id="files<?php echo $filecount; ?>" type="checkbox" name="files[]" value="<?php echo $file["date"]; ?>" /></td>
<td><?php echo link_for_file($file["name"], FALSE, 'style="font-weight: bold; color: #000;"'); ?></td>
<td><?php echo $file["size"]; ?></td>
<td><?php echo $file["comment"] ? str_replace("\\r\\n", "<br />", $file["comment"]) : ""; ?></td>
<td><?php echo date("d.m.Y H:i:s", $file["date"]) ?></td>
</tr>
<?php
    $filecount ++;
    }
?>
</tbody>
<?php
  if (($total_files > 1) && ($total_size > 0))
    {
    $tmp = '<tbody><tr class="flisttblftr">'.$nn.'<td>&nbsp;</td>'.$nn.'<td>Total:</td>'.$nn.'<td>'.bytesToKbOrMbOrGb($total_size).'</td>'.$nn.'<td>&nbsp;</td>'.$nn.'<td>&nbsp;</td>'.$nn.'</tr></tbody>';
    echo $tmp.'</table>';
    echo '<table id="table_filelist_f" cellpadding="3" cellspacing="1" class="filelist" align="left" style="position:absolute;left:0px;bottom:0px;">'.$tmp;
    }
  unset($total_files,$total_size);
  ?>
</table>
</div>
</div>
</form>
<?php
  }
?>
</td>
</tr>
</tbody>
</table>
<?php
		if ($options['flist_sort']) {
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {
  sorttable.makeSortable(document.getElementById('table_filelist'));
  $('#table_filelist_h tr.flisttblhdr td').each(function(id) {
    $(this).click((function (x) { return function() { $('#table_filelist tr.flisttblhdr td:eq('+x+')').click(); table_filelist_refresh_headers(); }; })(id));
  });
});
/* ]]> */
</script>
<?php
		}
?>
<!--Start Lix Checker-->
<table class="hide-table" id="tb4" cellspacing="5" width="100%">
<tbody>
<tr><td align="center" width="100%">
	<div style="text-align:center">
	<div align="center"><b><?php echo lang(267); ?></b></div>
<?php
// Print out workable sites for link checker
$name = array();
foreach ($sites as $i=>$v) {
	$name[] = $v['name'];
}
sort($name);
$workswith = '';
foreach ($name as $v) {
	$workswith .= $v.' | ';
}
$workswith = substr($workswith,0,-3);
?>
<div class="workswith"><?php echo $workswith; ?>
<br /><b><?php echo lang(268); ?></b><br />
Anonym.to | Linkbucks.com | Lix.in<br />
Rapidshare.com Folders | Usercash.com</div><br />
<div align="center">
<form action="ajax.php?ajax=linkcheck" method="post" id="linkchecker" onsubmit="return startLinkCheck();">
<textarea rows="10" cols="87" name="links" id="links"></textarea><br /><br />
<div style="text-align:center; margin:0 auto; width:450px;"><a href="<?php echo $PHP_SELF.'?debug=1' ?>" style="color:#3B5A6F"><b><?php echo lang(269); ?></b></a></div><br />
<?php echo lang(270); ?>: <input type="checkbox" value="d" name="d" />
<?php echo lang(271); ?>: <input type ="checkbox" value ="1" name="k" /><br /><br />
<input type="submit" id="submit" value="<?php echo lang(272); ?>" name="submit" />
</form>
</div>
<p style="text-align:center; font-size:10px">
	<small>Lix Checker v3.0.0 | Copyright Dman - MaxW.org | Optimized by zpikdum and sarkar<br /><b>Mod by eqbal | Ajax'd by TheOnly92</b></small></p><br />

<span id="loading" style="display: none;">
      &nbsp;&nbsp;
      <?php echo lang(273); ?>
      <img alt="<?php echo lang(274); ?>" src="templates/plugmod/images/ajax-loading.gif" name="pic1" />    </span>
<div align="center">
<div id="linkchecker-results" style="text-align: left;">
</div>
	</div></div>
</td>
</tr>
</tbody>
</table>
<!--End lix checker-->
<?php
if($_GET["act"])
  {
	echo '<script type="text/javascript">switchCell(3);</script>';
  }
elseif($_GET["debug"] || $_POST["links"])
  {
	echo '<script type="text/javascript">switchCell(4);</script>';
  }
else
  {
	echo '<script type="text/javascript">'."$('#navcell1').addClass('selected');</script>";
  }

?>
</td>
<td valign="top">&nbsp;</td>
</tr>
</tbody>
</table>
</td>
<td valign="top">
<!--Stat r-sidebar , Put your content in this block-->

<!-- End r-sidebar -->
</td>
</tr>
</tbody>
</table>
<br />
<table width="60%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td>
<div align="center"></div>
<script type="text/javascript">
var show = 0;
var show2 = 0;
</script>
<div align="center">
<?php
if ($options['file_size_limit'] > 0) {
	echo '<span style="color:#FFCC00">'.lang(337).' <b>' . bytesToKbOrMbOrGb ( $options['file_size_limit']*1024*1024 ) . '</b><br /></span>';
}
?>

<?php
$delete_delay = $options['delete_delay'];
if (is_numeric($delete_delay) && $delete_delay > 0){
	if($delete_delay > 3600){
		$ddelay = round($delete_delay/3600, 1);
		print '<span class="autodel">'.lang(282).': <b>'.$ddelay.'</b>&nbsp;'.lang(283).'</span>';
	}else{
		$ddelay = round($delete_delay/60);
		print '<span class="autodel">'.lang(282).': <b>'.$ddelay.'</b>&nbsp;'.lang(284).'</span>';
	}
}
?>
</div>
<div align="center" style="color:#ccc">
<?php if($options['server_info']) {
	ob_start();
?>
<div id="server_stats">
<?php	require_once(CLASS_DIR."sinfo.php"); ?>
</div>
<?php
  if ($options['ajax_refresh']) {
?>
<script type="text/javascript">var stats_timer = setTimeout("refreshStats()",10 * 1000);</script>
<?php
  }
	ob_end_flush();
}
?>
<hr />
<?php
print CREDITS;
?><br />
</div>
</td>
</tr>
</table>
<?php
if (($_GET["act"] == 'unrar_go') && !$options['disable_unrar']) {
  require_once(CLASS_DIR."options/unrar.php");
  unrar_go_go();
}
elseif (($_GET["act"] == 'rar_go') && !$options['disable_rar']) {
  require_once(CLASS_DIR."options/rar.php");
  rar_go_go();
}
?>
