<?php

function rar() {
  global $PHP_SELF, $list;
  if (!is_file(ROOT_DIR.'/rar/rar')) { echo lang(343).'<br><br>'; }
  else {
?>
  <form name="rar_files" method="post" action="<?php echo $PHP_SELF; ?>">
    <table align="center">
      <tr>
        <td>
          <table>
            <tr>
              <td colspan="2" style="border-right:1px solid #666; border-left:1px solid #666; border-top:1px solid #666; padding:6px; background-color:#001825;">
                <b><?php echo lang(344); ?></b>
              </td>
            </tr>
<?php
    for($i = 0; $i < count($_GET["files"]); $i++) {
      $file = $list[$_GET["files"][$i]];
?>
            <tr>
              <td style="border-left:1px solid #666; padding:2px; background-color:#001825;">
                <input type="hidden" name="files[<?php echo $i; ?>]" value="<?php echo $_GET["files"][$i]; ?>">
                <input type="hidden" name="rar_opts[filestorar][<?php echo $i; ?>]" value="<?php echo base64_encode(basename($file["name"])); ?>">
                <?php echo basename($file["name"]); ?>
              </td>
              <td style="border-right:1px solid #666; padding:2px; background-color:#001825;">
                <?php echo $file["size"]; ?>
              </td>
            </tr>
<?php
    }
?>
            <tr>
              <td colspan="2" style="border-right:1px solid #666; border-left:1px solid #666; border-top:1px solid #666; padding:6px; background-color:#001825;">
                <b><?php echo lang(345); ?></b>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="border-right:1px solid #666; border-left:1px solid #666; border-top:1px solid #666; padding:6px; background-color:#001825;">
                <input onKeyUp="javascript:this.value=this.value.replace(/[^a-z0-9\ \.\-\_]/gi,'_');" type="text" size="60" name="rar_opts[rarfilename]" value="<?php echo count($_GET["files"]) == 1 ? preg_replace("/[^a-z0-9\\040\\.\\-\\_]/i", '_', basename($file["name"])) : '' ; ?>">.rar
              </td>
            </tr>
            <tr>
              <td colspan="2" style="border-right:1px solid #666; border-left:1px solid #666; border-top:1px solid #666; padding:6px; background-color:#001825;">
                <b><?php echo lang(346); ?></b>
              </td>
            </tr>
            <tr>
              <td style="border-left:1px solid #666; padding:2px; background-color:#001825;">
                <?php echo lang(347); ?>
                <select name="rar_opts[comp_lvl]">
                	<option value="0" selected><?php echo lang(348); ?></option>
                	<option value="1"><?php echo lang(349); ?></option>
                	<option value="2"><?php echo lang(350); ?></option>
                	<option value="3"><?php echo lang(351); ?></option>
                	<option value="4"><?php echo lang(352); ?></option>
                	<option value="5"><?php echo lang(353); ?></option>
                </select>
              </td>
              <td style="border-right:1px solid #666; border-left:1px solid #666; padding:2px; background-color:#001825;">
                <input type="checkbox" name="rar_opts[vols]" value="1" onClick="javascript:var displ=this.checked?'inline':'none';document.getElementById('rar_opts_vols').style.display=displ;var fc=document.getElementsByName('rar_opts[vols_s]')[0]; fc.focus(); fc.selectionStart = 0; fc.selectionEnd = fc.value.length;"><?php echo lang(354); ?>
                <span id="rar_opts_vols" style="display:none">
                  <br>Size: <input type="text" size="3" name="rar_opts[vols_s]" value="1">&nbsp;
                  <select name="rar_opts[vols_sm]">
                  	<option value="0">bytes</option>
                  	<option value="1">kilobytes(*1024)</option>
                  	<option value="2">Kilobytes(*1000)</option>
                  	<option value="3" selected>megabytes(*1024)</option>
                  	<option value="4">Megabytes(*1000)</option>
                  	<option value="5">gigabytes(*1024)</option>
                  	<option value="6">Gigabytes(*1000)</option>
                  </select>
                </span>
              </td>
            </tr>
            <tr>
              <td style="border-left:1px solid #666; padding:2px; background-color:#001825;">
                <input type="checkbox" name="rar_opts[delete]" value="1"><?php echo lang(355); ?>
              </td>
              <td style="border-right:1px solid #666; border-left:1px solid #666; padding:2px; background-color:#001825;">
                <input type="checkbox" name="rar_opts[solid]" value="1"><?php echo lang(356); ?>
              </td>
            </tr>
            <tr>
              <td style="border-left:1px solid #666; padding:2px; background-color:#001825;">
                <input type="checkbox" name="rar_opts[rec_rec]" value="1" onClick="javascript:var displ=this.checked?'inline':'none';document.getElementById('rar_opts_rec_rec').style.display=displ; var fc=document.getElementsByName('rar_opts[rec_rec_s]')[0]; fc.focus(); fc.selectionStart = 0; fc.selectionEnd = fc.value.length;"><?php echo lang(357); ?>
                <span id="rar_opts_rec_rec" style="display:none">
                  <br>From 1 to 10: <input type="text" size="3" name="rar_opts[rec_rec_s]" value="1">%
                </span>
              </td>
              <td style="border-right:1px solid #666; border-left:1px solid #666; padding:2px; background-color:#001825;">
                <input type="checkbox" name="rar_opts[test]" value="1"><?php echo lang(358); ?>
              </td>
            </tr>
            <tr>
              <td style="border-left:1px solid #666; padding:2px; background-color:#001825;">
                <input type="checkbox" name="rar_opts[use_pass1]" value="1" onClick="javascript:var displ=this.checked?'inline':'none';document.getElementById('rar_opts_pass').style.display=displ;document.getElementsByName('rar_opts[pass]')[0].focus();"><?php echo lang(359); ?>
                <span id="rar_opts_pass" style="display:none">
                  <br>
                  <input type="password" size="15" name="rar_opts[pass]" value="">
                  <br>
                  <input type="checkbox" name="rar_opts[use_pass2]" value="1"><?php echo lang(360); ?>
                </span>
              </td>
              <td style="border-right:1px solid #666; border-left:1px solid #666; padding:2px; background-color:#001825;">
                <input type="checkbox" name="rar_opts[path_i]" value="1" onClick="javascript:var displ=this.checked?'inline':'none';document.getElementById('rar_opts_path_i').style.display=displ;document.getElementsByName('rar_opts[path_i_path]')[0].focus();"><?php echo lang(361); ?>
                <span id="rar_opts_path_i" style="display:none;"><br><input type="text" size="15" name="rar_opts[path_i_path]" value=""></span>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="border-right:1px solid #666; border-left:1px solid #666; padding:2px; background-color:#001825;">
                &nbsp;
              </td>
            </tr>
            <tr>
              <td colspan="2" style="border-top:1px solid #666;">&nbsp;</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center">
          <input type="hidden" name="act" value="rar_go">
          <input type="submit" value="<?php echo lang(362); ?>">
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
  </form>
<?php
  }
}





function rar_go() {
  global $PHP_SELF;
  require_once(CLASS_DIR."rar.php");
?>
  <table align="center">
    <tr>
      <td>
        <table>
<?php
  $_GET['rar_opts']['rarfilename'] = trim(basename(stripslashes($_GET['rar_opts']['rarfilename'])));
  if (substr(strtolower($_GET['rar_opts']['rarfilename']), -4) != '.rar') { $_GET['rar_opts']['rarfilename'] .= '.rar'; }
?>
          <tr>
            <td colspan="2" style="border-right:1px solid #666; border-left:1px solid #666; border-top:1px solid #666; padding:6px; background-color:#001825;">
              <?php printf(lang(363),$_GET['rar_opts']['rarfilename']); ?>
            </td>
          </tr>
          <tr>
            <td style="border-left:1px solid #666; padding:2px; background-color:#001825;"><?php echo lang(374); ?></td>
            <td id="rar_status" style="border-right:1px solid #666; padding:2px; background-color:#001825;"><?php echo lang(364); ?></td>
          </tr>
          <tr>
            <td colspan="2" style="border-top:1px solid #666;">&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>
      </td>
    </tr>
  </table>
  <span id="rar_finished" style="display:none;"><a href="<?php echo $PHP_SELF."?act=files"; ?>"><?php echo lang(365); ?></a><br><br><br></span>
<?php
}




function rar_go_go() {
  global $check_these_before_unzipping, $download_dir, $forbidden_filetypes, $list;
?>
<script type="text/javascript">
function rar_st(elementid, st){
  document.getElementById(elementid).innerHTML = st;
  return true;
}
</script>
<script type="text/javascript">switchCell(3);</script>
<?php
  flush();
  require_once(CLASS_DIR."rar.php");
  $rar = new rlRar(stripslashes($_GET['rar_opts']['rarfilename']), $check_these_before_unzipping ? $forbidden_filetypes : array('.xxx'));
  if ($rar->rar_return !== 'rar') {
?>
<script type="text/javascript">rar_st('rar_status', '<?php echo lang(343); ?>');</script>
<?php 
  }
  else {
    $rar_result = $rar->addtoarchive($_GET['rar_opts'], $download_dir, 'rar_status');
    echo $rar_result;
    if (strpos($rar_result, ", 'Done')") !== false) {
      _create_list();
      clearstatcache();
      if ($_GET['rar_opts']['delete'] == true) {
        foreach ($_GET['rar_opts']['filestorar'] as $rar_tounlist) {
          $rar_tounlist = basename(base64_decode($rar_tounlist));
          if ($rar_tounlist === false) { continue; }
          $rar_tounlist = realpath($download_dir).'/'.$rar_tounlist;
          if (is_file($rar_tounlist)) { continue; }
          foreach ($list as $list_key => $list_item) {
            if ($list_item["name"] === $rar_tounlist) { unset($list[$list_key]); }
          }
        }
      }
      $rar_tolist = realpath($download_dir).'/'.basename($rar->filename);
      if ($_GET['rar_opts']['vols'] && !is_file($rar_tolist)) {
        if (substr(strtolower($rar_tolist), -4) == '.rar') { $rar_tolist = substr($rar_tolist, 0, -4); }
        $tmp = basename(strtolower($rar_tolist)).'.part';
        $rar_dir = opendir(realpath($download_dir).'/');
        while (false !== ($rar_f_dd = readdir($rar_dir))) {
          $rar_f_dd_ = basename(strtolower($rar_f_dd));
          if ($tmp == substr($rar_f_dd_, 0, strlen($tmp)) && is_numeric(substr($rar_f_dd_, strlen($tmp), -4))) {
            $rar_f_dd = realpath($download_dir).'/'.basename($rar_f_dd);
            $time = filemtime($rar_f_dd); while (isset($list[$time])) { $time++; }
            $list[$time] = array("name" => $rar_f_dd, "size" => bytesToKbOrMbOrGb(filesize($rar_f_dd)), "date" => $time);
          }
        }
        closedir($rar_dir);
      }
      elseif (is_file($rar_tolist)) {
        $time = filemtime($rar_tolist); while (isset($list[$time])) { $time++; }
        $list[$time] = array("name" => $rar_tolist, "size" => bytesToKbOrMbOrGb(filesize($rar_tolist)), "date" => $time);
      }
      if (!updateListInFile($list)) {
?>
<script type="text/javascript">var tmp = document.getElementById('rar_finished'); tmp.innerHTML = "<?php echo lang(9); ?><br><br>" + tmp.innerHTML</script>;
<?php
      }
    }
?>
<script type="text/javascript">document.getElementById('rar_finished').style.display = 'inline';</script>
<?php
  }
}
?>