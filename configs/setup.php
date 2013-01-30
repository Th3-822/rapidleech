<?php
////////////////////////////////////////////////////////////////////////////////
//Rapidleech setup file
//
//If you need to do rapidleech setup again:
// To load default config: delete 'configs/config.php'
// To work on your old config: rename 'configs/config.php' to 'configs/config_old.php'
// After that, go to your rapidleech url to access setup
//
////////////////////////////////////////////////////////////////////////////////

$PHP_SELF = $_SERVER['SCRIPT_NAME'];
if (!defined('RAPIDLEECH')) define('RAPIDLEECH', 'yes');
if (!defined('CONFIG_DIR')) define('CONFIG_DIR', 'configs/');

//Default options file
require_once(CONFIG_DIR.'default.php');
//Exit setup if config file exists and is complete
if (is_file(CONFIG_DIR.'config.php')) {
	require_once(CONFIG_DIR.'config.php');  
	if (count($options) == count($default_options)) return;
}

define('TEMPLATE_DIR', 'templates/plugmod/');
//$options['default_language'] = "en";
require_once('classes/other.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Rapidleech Setup</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link title="Rapidleech Style" href="<?php echo TEMPLATE_DIR; ?>styles/rl_style_pm.css" rel="stylesheet" type="text/css" />
<?php

if (!isset($_POST['setup_save'])) {
  function js_special_chars($t) {
    return str_replace(array('\\', "'", '"', '&', "\n", "\r", "\t", chr(8), chr(12)), array("\\", "\\'", "\\\"", "\&", '\\n', "\\r", "\\t", "\\b", "\\f"), $t);
  }
?>
<script src="classes/js.js" type="text/javascript"></script>
<script type="text/javascript">
/* <![CDATA[ */
function load_current_config() {
<?php
$options = array(); $old_options = false;
if (is_file(CONFIG_DIR."config.php")) { include(CONFIG_DIR."config.php"); $old_options = true; }
elseif (is_file(CONFIG_DIR."config_old.php")) { include(CONFIG_DIR."config_old.php"); $old_options = true; }

foreach ($default_options as $k => $v) { if (!array_key_exists($k, $options)) { $options[$k] = $v; } }

foreach ($options as $k => $v) {
  if (!array_key_exists($k, $default_options) || is_array($default_options[$k])) { continue; }
  $v = js_special_chars($v);
  if (is_bool($default_options[$k])) {
    echo "  $('#opt_{$k}').".($v ? "attr('checked', 'checked')" : "removeAttr('checked')").";\n";
  }
  elseif (is_numeric($default_options[$k])) {
    $v = floor($v);
    echo "  set_element_val('opt_{$k}', '".($k == 'delete_delay' ? $v."', '".floor($v/60) : $v)."');\n";
  }  
  else { echo "  set_element_val('opt_{$k}', '{$v}');\n"; }
}
?>
  $('#opt_forbidden_filetypes').val('<?php
foreach ($options['forbidden_filetypes'] as $k => $v) {
  echo js_special_chars($v).(count($options['forbidden_filetypes'])-1 == $k ? '' : ', ');
}
?>');
  while ($('#opt_login_table tbody>tr').size() < <?php echo count($options['users']); ?>) { $("#opt_login_add").click(); }
  while ($('#opt_login_table tbody>tr').size() > <?php echo max(1, count($options['users'])); ?>) { $('#opt_login_table tbody>tr:last').remove(); }
<?php
$i = 0;
foreach ($options['users'] as $k => $v) {
  $k = js_special_chars($k); $v = js_special_chars($v);
  echo "  $('#opt_login_table [name=users[]]').eq({$i}).val('{$k}');\n";
  echo "  $('#opt_login_table [name=passwords[]]').eq({$i}).val('{$v}');\n";
  $i++;
}
?>
  if ($('#opt_forbidden_filetypes_block').attr('checked')) { $('#opt_rename_these_filetypes_to_0').hide(); }
  else { $('#opt_rename_these_filetypes_to_0').show(); }
  if ($('#opt_login').attr('checked')) { $('#opt_login_0').show(); }
  else { $('#opt_login_0').hide(); }
  if ($('#opt_new_window').attr('checked')) { $('#opt_new_window_0').show(); }
  else { $('#opt_new_window_0').hide(); }
}

function set_element_val(id, value, display) {
  display = (typeof display == 'undefined') ? value : display;
  var e = $('#'+id);
  e.val(value); if (e.val() != value) { e.append($('<option><\/option>').val(value).html(display)); e.val(value); }
}

function save_config() {
  document.setup_form.submit();
}

$(document).ready(function() {
  $("#save").removeAttr("disabled");
  $("#reset").removeAttr("disabled");
  $('#save').click(function() { save_config(); });
  $('#reset').click(function() { load_current_config(); });

  $('div.div_title').append('&nbsp;v');
  $('div.div_title').click(function() {
    var t =  $(this).parent().children('div:not(.div_title)');
    if (t.is(':visible')) { t.hide(); $(this).text($(this).text().slice(0, - 1)+'>'); }
    else { t.show(); $(this).text($(this).text().slice(0, -1)+'v'); }
  });
  $('#div_main_advanced').click();

  $('#opt_login_cgi').click(function() {
    if ($(this).attr('checked')) { alert('Verify that main .htaccess is writeable before saving the config so the CGI fix can be applied correctly.'); }
  });
  $('#opt_disable_actions').click(function() {
    if ($(this).attr('checked')) { $('#opt_actions_table :checkbox:not(#opt_disable_deleting)').each(function() { $(this).attr('checked', 'checked'); }); }
    else { $('#opt_actions_table :checkbox:not(#opt_disable_deleting)').removeAttr('checked'); }
  });
  $('#opt_disable_deleting').click(function() {
    if ($(this).attr('checked')) {
      $('#opt_disable_delete').attr('checked', 'checked');
    }
    else { $('#opt_disable_delete').removeAttr('checked'); }
  });
  $("#opt_forbidden_filetypes_block").click(function() { $('#opt_rename_these_filetypes_to_0').toggle(); } );
  $("#opt_new_window").click(function() { $('#opt_new_window_0').toggle(); } );
  $("#opt_login").click(function() { $('#opt_login_0').toggle(); } );
  $("#opt_login_add").click(function() {
    var row = $('#opt_login_table tbody>tr:last').clone(true).insertAfter('#opt_login_table tbody>tr:last');
    $('td:eq(0)', row).html('<input type="button" value="Remove" onclick="$(this).parent().parent().remove();" />');
    $('td:eq(1)>input,td:eq(2)>input', row).val('');
    return false;
  });

  $('#opt_delete_delay').change(function() {
    if ($(this).val() == 'other') {
      var other = parseInt(prompt('How many minutes?', '0'), 10) || 0;
      set_element_val('opt_delete_delay', other*60, other);
    }
  });
  $('#opt_file_size_limit').change(function() {
    if ($(this).val() == 'other') {
      var other = parseInt(prompt('How many MiBs?', '0'), 10) || 0;
      set_element_val('opt_file_size_limit', other);
    }
  });

  load_current_config();
});
/* ]]> */
</script>
<?php
}
?>
<style type="text/css">
<!--
#opt_actions_table table td { padding-right: 15px; text-align: left; }
#opt_presentation_table table td { padding-right: 10px; text-align: left; }
#opt_advanced_table table td { min-width: 80px; text-align: left; }
#opt_login_table thead td { padding-bottom: 5px; }
#opt_login_table td { text-align: center; }
.div_error {
  font-weight: bold; font-size: large; text-align: center; color:#FF0000;
}
.div_opt {
  text-align: left;
  padding-bottom: 5px;
}
.table_cat {
  min-width: 300px;
}
.table_opt {
  width: 100%;
}
.div_main {
  text-align: center;
  border: 1px white ridge;
  padding: 5px;
  margin-top:5px;
}
.div_message {
  color: #FFB000;
  font-weight: bold;
  font-size: larger;
  text-align: center;
  margin: 10px;
}
.div_setup {
  color: #FF7700;
  font-weight: bold;
  font-size: large;
  text-align: center;
}
.div_title {
  color: #FFB000;
  font-size: larger;
  font-weight: bold;
  margin-bottom: 5px;
}
-->
</style>
</head>
<body>
<center><img src="<?php echo TEMPLATE_DIR; ?>images/logo_pm.gif" alt="RapidLeech PlugMod" border="0" /></center>
<br />
<noscript><div class="div_error">This page won't work without JavaScript, please enable JavaScript and refresh the page.</div></noscript>
<?php
if (isset($_POST['setup_save']) && $_POST['setup_save'] == 1) {

  $options = array();
  foreach ($default_options as $k => $v) { if (!array_key_exists($k, $options)) { $options[$k] = $v; } }
  
  foreach($default_options as $k => $v) {
    if (is_array($default_options[$k])) { continue; }
    if (is_bool($default_options[$k])) {
      $options[$k] = (isset($_POST['opt_'.$k]) && $_POST['opt_'.$k] ? true : false);
    }
    elseif (is_numeric($default_options[$k])) {
      $options[$k] = (isset($_POST['opt_'.$k]) && $_POST['opt_'.$k] ? floor($_POST['opt_'.$k]) : 0);
    }  
    else {
      $options[$k] = (isset($_POST['opt_'.$k]) && $_POST['opt_'.$k] ? stripslashes($_POST['opt_'.$k]) : '');
    }
  }
  
  function array_trim(&$v) { $v = trim($v); }
  $tmp = (isset($_POST['opt_forbidden_filetypes']) ? stripslashes($_POST['opt_forbidden_filetypes']) : '');
  $tmp = explode(',', $tmp);
  array_walk($tmp, 'array_trim');
  $tmp = (count($tmp) > 0 && strlen(trim($tmp[0])) > 0 ? $tmp : array());
  $options['forbidden_filetypes'] = $tmp;

  $tmp = "\r\n\r\n<IfModule mod_rewrite.c>\r\nRewriteEngine on\r\nRewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]\r\n</IfModule>";
  $htacess = @file_get_contents('.htaccess');
  if (empty($htacess)) { echo 'It was not possible to read .htacess file<br /><br />'; }
  elseif (isset($_POST['opt_login_cgi']) && $_POST['opt_login_cgi']) {
    if (strpos($htacess, $tmp) === false) { if (!@write_file(".htaccess", $htacess.$tmp, 1)) {
      echo 'It was not possible to edit .htacess file to enable CGI authorization fix<br /><br />';
      $options['login_cgi'] = false;
    }
    }
  }
  else {
    if (strpos($htacess, $tmp) !== false) { if (!@write_file(".htaccess", str_replace($tmp, '', $htacess))) {
      echo 'It was not possible to write .htacess file to completely disable CGI authorization fix<br /><br />';
    } }
  }

  $options['users'] = array();
  if (isset($_POST['users']) && isset($_POST['passwords']) && 
  count($_POST['users']) > 0 && count($_POST['users']) == count($_POST['passwords'])) {
    foreach ($_POST['users'] as $k => $u) {
      $u = stripslashes($u); $p = stripslashes($_POST['passwords'][$k]);
      if ($u == '' && $p == '') { continue; }
      $options['users'][$u] = $p;
    }
  }
  else { echo 'There was a problem with users and passwords<br /><br />'; }
  
  ob_start(); var_export($options); $opt = ob_get_contents(); ob_end_clean();
  $opt = (strpos($opt, "\r\n") === false ? str_replace(array("\r", "\n"), "\r\n", $opt) : $opt);
  $opt = "<?php\r\n if (!defined('RAPIDLEECH')) { require_once('index.html'); exit; }\r\n\r\n\$options = ".
        $opt.
        "; \r\n\r\nrequire_once('site_checker.php');\r\nrequire_once('accounts.php');\r\n?>";
  if (!@write_file(CONFIG_DIR."config.php", $opt, 1)) { echo '<div class="div_error">It was not possible to write the configuration<br />Set permissions of "configs" folder to 0777 and try again</div>'; }
  else {
    if (is_file(CONFIG_DIR.'config_old.php')) { if (@!unlink(CONFIG_DIR.'config_old.php') && is_file(CONFIG_DIR.'config_old.php')) { '<div class="div_message">It was not possible to delete the old configuration.<br />Manually delete "configs/config_old.php"</div><br />'; } }
    echo '<div class="div_message">Configuration saved! Click <a href="'.$PHP_SELF.'">here</a> to continue to rapidleech</div>';
  }
?>
<?php
}
else {
?>
<div class="div_setup">Rapidleech Setup</div>

<div class="div_message"><?php echo ($old_options ? 'Old' : 'Default'); ?> rapidleech options loaded</div>

<form method="post" enctype="multipart/form-data" name="setup_form" action="<?php echo $PHP_SELF; ?>">
<table align="center" class="table_cat">
  <tr><td>

    <div class="div_main">
      <div class="div_title">General Options</div>
      <div class="div_opt">
        <table class="table_opt">
          <tr><td>Download Directory</td><td><input type="text" id="opt_download_dir" name="opt_download_dir" /></td></tr>
          <tr><td>Allow users to change<br />download directory</td><td><input type="checkbox" value="1" name="opt_download_dir_is_changeable" id="opt_download_dir_is_changeable" /></td></tr>
          <tr><td>Auto Delete in minutes</td><td> <select size="1" name="opt_delete_delay" id="opt_delete_delay">
                                                	<option value="0">Disabled</option>
                                                	<option value="3600">60</option>
                                                	<option value="7200">120</option>
                                                	<option value="10800">180</option>
                                                	<option value="14400">240</option>
                                                	<option value="18000">300</option>
                                                	<option value="other">Other</option>
                                                  </select></td></tr>
          <tr><td>File name prefix</td><td><input type="text" id="opt_rename_prefix" name="opt_rename_prefix" /></td></tr>
          <tr><td>File name suffix</td><td><input type="text" id="opt_rename_suffix" name="opt_rename_suffix" /></td></tr>
          <tr><td>Replace spaces for<br />underscore in file names</td><td><input type="checkbox" value="1" name="opt_rename_underscore" id="opt_rename_underscore" /></td></tr>
          <tr><td>Bandwidth saving</td><td><input type="checkbox" value="1" name="opt_bw_save" id="opt_bw_save" /></td></tr>
          <tr><td>File size limit in MiB</td><td> <select size="1" name="opt_file_size_limit" id="opt_file_size_limit">
                                                	<option value="0">Disabled</option>
                                                	<option value="100">100</option>
                                                	<option value="200">200</option>
                                                	<option value="500">500</option>
                                                	<option value="700">700</option>
                                                	<option value="1000">1000</option>
                                                	<option value="other">Other</option>
                                                  </select></td></tr>
          <tr><td>Disable auto download feature</td><td><input type="checkbox" value="1" name="opt_auto_download_disable" id="opt_auto_download_disable" /></td></tr>
          <tr><td>Disable auto upload feature</td><td><input type="checkbox" value="1" name="opt_auto_upload_disable" id="opt_auto_upload_disable" /></td></tr>
          <tr><td>Disable notes feature</td><td><input type="checkbox" value="1" name="opt_notes_disable" id="opt_notes_disable" /></td></tr>
          <tr><td>Disable *.upload.html creation</td><td><input type="checkbox" value="1" name="opt_upload_html_disable" id="opt_upload_html_disable" /></td></tr>
          <tr><td>Disable myuploads.txt creation</td><td><input type="checkbox" value="1" name="opt_myuploads_disable" id="opt_myuploads_disable" /></td></tr>
        </table>
      </div>
    </div>

  </td></tr>
</table><table align="center" class="table_cat">
  <tr><td>

    <div class="div_main">
      <div class="div_title">Authorization mode</div>
      <div class="div_opt">
        <input type="checkbox" value="1" name="opt_login" id="opt_login" /> Enable <b>Authorization mode</b>
        <div style="text-align: left;" id="opt_login_0">
          <input type="checkbox" value="1" name="opt_login_cgi" id="opt_login_cgi" /> Enable CGI authorization fix<br />
          <table id="opt_login_table" class="table_opt">
          <thead>
            <tr><td><input id="opt_login_add" type="button" value="Add user" /></td><td><b>User</b></td><td><b>Password</b></td></tr>
          </thead><tbody>
            <tr><td>&nbsp;</td><td><input type="text" name="users[]" size="14" /></td><td><input type="text" name="passwords[]" size="14" /></td></tr>
          </tbody></table>
        </div>
      </div>
    </div>

  </td></tr>
</table><table align="center" class="table_cat">
  <tr><td>

    <div class="div_main">
      <div class="div_title">Presentation Options</div>
      <div class="div_opt" id="opt_presentation_table">
        <table class="table_opt"><tr>
          <td style="vertical-align: top;">
            <table>
              <tr><td>Template</td><td>
                <select size="1" name="opt_template_used" id="opt_template_used">
<?php
$d = dir('templates/');
while (false !== ($f = $d->read())) {
  if (!is_dir('templates/'.$f) || $f == '.' || $f == '..') { continue; }
  echo '<option value="'.$f.'">'.$f.'</option>';
}
$d->close();
?>
                </select>
              </td></tr>
              <tr><td>Language</td><td>
                <select size="1" name="opt_default_language" id="opt_default_language">
<?php
$d = dir('languages/');
while (false !== ($f = $d->read())) {
  if (substr($f, -4) != '.php') { continue; }
  echo '<option value="'.substr($f, 0, -4).'">'.substr($f, 0, -4).'</option>';
}
$d->close();
?>
                </select>
              </td></tr>
            </table>
          </td>
          <td style="vertical-align: top;">
            <table>
              <tr><td>Show all files, not<br />only downloaded</td><td><input type="checkbox" value="1" name="opt_show_all" id="opt_show_all" /></td></tr>
              <tr><td>Auto Refresh Server Info</td><td><input type="checkbox" value="1" name="opt_ajax_refresh" id="opt_ajax_refresh" /></td></tr>
              <tr><td>CPU, Memory &amp; Time Info</td><td><input type="checkbox" value="1" name="opt_server_info" id="opt_server_info" /></td></tr>
            </table>
          </td>
        </tr><tr>
          <td style="vertical-align: top;" colspan="2">
          &nbsp;
          </td>
        </tr><tr>
          <td style="vertical-align: top;">
            <table>
              <tr><td>Make file list sortable</td><td><input type="checkbox" value="1" name="opt_flist_sort" id="opt_flist_sort" /></td></tr>
              <tr><td>Fixed file list header<br />(May not work in all browsers)</td><td><input type="checkbox" value="1" name="opt_flist_h_fixed" id="opt_flist_h_fixed" /></td></tr>
            </table>
          </td>
          <td style="vertical-align: top;">
            <table>
              <tr><td>Transload files in a new window</td><td><input type="checkbox" value="1" name="opt_new_window" id="opt_new_window" /></td></tr>
              <tr id="opt_new_window_0"><td>Use javascript window</td><td><input type="checkbox" value="1" name="opt_new_window_js" id="opt_new_window_js" /></td></tr>
            </table>
          </td>
          <td style="vertical-align: top;">
          &nbsp;
          </td>
        </tr></table>
      </div>
    </div>

  </td></tr>
</table><table align="center" class="table_cat">
  <tr><td>

    <div class="div_main">
      <div class="div_title">File Actions Restrictions</div>
      <div class="div_opt" id="opt_actions_table">
        <table class="table_opt">
          <tr><td>Disable all actions</td><td><input type="checkbox" value="1" name="opt_disable_actions" id="opt_disable_actions" /></td></tr>
          <tr><td>Disable deleting on all actions(except delete)</td><td><input type="checkbox" value="1" name="opt_disable_deleting" id="opt_disable_deleting" /></td></tr>
          <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
          <tr>
            <td style="vertical-align: top;"><table>
              <tr><td>Disable delete action</td><td><input type="checkbox" value="1" name="opt_disable_delete" id="opt_disable_delete" /></td></tr>
              <tr><td>Disable rename action</td><td><input type="checkbox" value="1" name="opt_disable_rename" id="opt_disable_rename" /></td></tr>
              <tr><td>Disable massive rename</td><td><input type="checkbox" value="1" name="opt_disable_mass_rename" id="opt_disable_mass_rename" /></td></tr>
              <tr><td>Disable massive email</td><td><input type="checkbox" value="1" name="opt_disable_mass_email" id="opt_disable_mass_email" /></td></tr>
              <tr><td>Disable email</td><td><input type="checkbox" value="1" name="opt_disable_email" id="opt_disable_email" /></td></tr>
              <tr><td>Disable FTP</td><td><input type="checkbox" value="1" name="opt_disable_ftp" id="opt_disable_ftp" /></td></tr>
              <tr><td>Disable upload</td><td><input type="checkbox" value="1" name="opt_disable_upload" id="opt_disable_upload" /></td></tr>
            </table></td>
            <td style="vertical-align: top;"><table>
              <tr><td>Disable merge</td><td><input type="checkbox" value="1" name="opt_disable_merge" id="opt_disable_merge" /></td></tr>
              <tr><td>Disable split</td><td><input type="checkbox" value="1" name="opt_disable_split" id="opt_disable_split" /></td></tr>
              <tr><td>Disable md5 change</td><td><input type="checkbox" value="1" name="opt_disable_md5_change" id="opt_disable_md5_change" /></td></tr>
              <tr><td>Disable md5</td><td><input type="checkbox" value="1" name="opt_disable_md5" id="opt_disable_md5" /></td></tr>
              <tr><td>Disable list</td><td><input type="checkbox" value="1" name="opt_disable_list" id="opt_disable_list" /></td></tr>
            </table></td>
            <td style="vertical-align: top;"><table>
              <tr><td>Disable compression<br />(tar, zip, rar)</td><td><input type="checkbox" value="1" name="opt_disable_archive_compression" id="opt_disable_archive_compression" /></td></tr>
              <tr><td>Disable tar</td><td><input type="checkbox" value="1" name="opt_disable_tar" id="opt_disable_tar" /></td></tr>
              <tr><td>Disable zip</td><td><input type="checkbox" value="1" name="opt_disable_zip" id="opt_disable_zip" /></td></tr>
              <tr><td>Disable unzip</td><td><input type="checkbox" value="1" name="opt_disable_unzip" id="opt_disable_unzip" /></td></tr>
              <tr><td>Disable rar</td><td><input type="checkbox" value="1" name="opt_disable_rar" id="opt_disable_rar" /></td></tr>
              <tr><td>Disable unrar</td><td><input type="checkbox" value="1" name="opt_disable_unrar" id="opt_disable_unrar" /></td></tr>
            </table></td>
          </tr>

        </table>
      </div>
    </div>

  </td></tr>
</table><table align="center" class="table_cat">
  <tr><td>

    <div class="div_main">
      <div class="div_title" id="div_main_advanced">Advanced Options</div>
      <div class="div_opt" id="opt_advanced_table">
        <div style="text-align: center; padding-bottom: 10px;">(You don't need to change these unless you know what you are doing)</div>
        <table class="table_opt">
          <tr><td>Try to list files bigger than 2gb<br />(32 bit o.s.)</td><td><input type="checkbox" value="1" name="opt_2gb_fix" id="opt_2gb_fix" /></td></tr>
          <tr><td colspan="2" style="text-align: center;">Forbidden file types</td></tr>
          <tr><td colspan="2" style="text-align: center;"><input size="50" type="text" id="opt_forbidden_filetypes" name="opt_forbidden_filetypes" /></td></tr>
          <tr><td>Block download of forbidden file types</td><td><input type="checkbox" value="1" name="opt_forbidden_filetypes_block" id="opt_forbidden_filetypes_block" /></td></tr>
          <tr id="opt_rename_these_filetypes_to_0"><td>Rename forbidden file types to</td><td><input type="text" size="8" value="" name="opt_rename_these_filetypes_to" id="opt_rename_these_filetypes_to" /></td></tr>
          <tr><td>Block forbidden file types for file actions</td><td><input type="checkbox" value="1" name="opt_check_these_before_unzipping" id="opt_check_these_before_unzipping" /></td></tr>
          <tr><td>Images via php</td><td><input type="checkbox" value="1" name="opt_images_via_php" id="opt_images_via_php" /></td></tr>
          <tr><td>Use curl as an alternatif for stream socket client</td><td><input type="checkbox" value="1" name="opt_use_curl" id="opt_use_curl" /></td></tr>
          <tr><td>Redirect passive method</td><td><input type="checkbox" value="1" name="opt_redir" id="opt_redir" /></td></tr>
          <tr><td>No cache</td><td><input type="checkbox" value="1" name="opt_no_cache" id="opt_no_cache" /></td></tr>
          <tr><td>fgc</td><td><input type="text" value="" size="2" name="opt_fgc" id="opt_fgc" /></td></tr>
        </table>
      </div>
    </div>
    
  </td></tr>
</table>

<table border="0" cellpadding="10" align="center">
  <tr>
    <td>
      <div style="text-align: center;">
        <input type="hidden" value="1" name="setup_save" />
        <input type="button" value="Save Configuration" id="save" name="save" disabled="disabled" />
        <input type="button" value="Reset" id="reset" name="reset" disabled="disabled" />
      </div>
    </td>
  </tr>
</table>
</form>
<?php
}
?>
</body>
</html><?php exit; ?>