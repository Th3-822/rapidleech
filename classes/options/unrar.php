<?php

function unrar() {
  global $PHP_SELF, $options, $list;
  require_once(CLASS_DIR."rar.php");
?>
<script type="text/javascript">
/* <![CDATA[ */
function unrar_setCheckboxes(act, filestounrar) {
  var elts = document.getElementsByName('filestounrar['+filestounrar+'][]');
  var elts_cnt = (typeof(elts) != 'undefined') ? elts.length : 0;
  if (elts_cnt) {
    for (var i = 0; i < elts_cnt; i++) {
      elts[i].checked = (act == 1 || act == 0) ? act : elts[i].checked ? 0 : 1;
    }
  }
}
/* ]]> */
</script>
  <form name="unrar_files" method="post" action="<?php echo $PHP_SELF; ?>">
    <table align="center">
      <tr>
        <td>
          <table>
<?php
  $rar_passl_needed = false;
  $any_file_tounrar = false;
  for($i = 0; $i < count($_GET["files"]); $i++) {
    $file = $list[$_GET["files"][$i]];
?>
            <tr align="center">
              <td colspan="2" class="unrar-main-td">
                <input type="hidden" name="files[<?php echo $i; ?>]" value="<?php echo $_GET["files"][$i]; ?>" />
                <?php printf(lang(366),htmlentities(basename($file["name"]))); ?>
                <br />
                <a href="javascript:unrar_setCheckboxes(1, <?php echo $i;?>);"><?php echo lang(52); ?></a> |
                <a href="javascript:unrar_setCheckboxes(0, <?php echo $i;?>);"><?php echo lang(53); ?></a> |
                <a href="javascript:unrar_setCheckboxes(2, <?php echo $i;?>);"><?php echo lang(54); ?></a>
              </td>
            </tr>
            <tr>
              <td colspan="2" class="unrar-main2-td">
                &nbsp;
<?php
    unset ($rar);
    $rar = new rlRar($file["name"], $options['check_these_before_unzipping'] ? $options['forbidden_filetypes'] : array('.xxx'));
    if ($rar->get_rar_return() === false) { echo lang(367); }
    else {
      $rar_list = $rar->listthem(@$_GET['passwords'][$i], $options['download_dir'], $i);
      if ($rar_list[0] == 'PASS') { $rar_passl_needed = true; echo lang(368); }
      elseif ($rar_list['NEEDP'] == true) { echo lang(369); }
      elseif ($rar_list[0] == 'ERROR') { printf(lang(370),$rar_list[1].' '.$rar_list[2]); }
    }
?>
                <input type="<?php echo ($rar_list['NEEDP'] == true) ? 'password' : 'hidden'; ?>" name="passwords[]" value="<?php echo $_GET['passwords'][$i]; ?>" />
                &nbsp;
              </td>
            </tr>
<?php
    if ($rar_list[0] == 'LIST') {
      $any_file_tounrar = true;
      $rar_list = $rar_list[2];
      foreach($rar_list as $rar_key => $rar_item) {
?>
            <tr>
              <td class="unrar-lb-td">
                <input type="checkbox" name="filestounrar[<?php echo $i; ?>][]" checked="checked" value="<?php echo base64_encode($rar_key); ?>" />
              </td>
              <td class="unrar-rb-td"><?php echo $rar_key.' ('.bytesToKbOrMbOrGb($rar_item['size']).')'; ?></td>
            </tr>
<?php
      }
    }
?>
            <tr>
              <td colspan="2" class="unrar-space-td">&nbsp;</td>
            </tr>
<?php
  }
?>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center">
          <input type="hidden" name="act" value="<?php echo $rar_passl_needed ? 'unrar' : 'unrar_go'; ?>" />
          <input type="submit" value="<?php echo $rar_passl_needed ? lang(371) : lang(372); ?>" />
        </td>
      </tr>
      <tr>
        <td>
        </td>
      </tr>
    </table>
  </form>
<?php
}




function unrar_go() {
  global $PHP_SELF, $options, $list;
  require_once(CLASS_DIR."rar.php");
?>
  <table align="center">
    <tr>
      <td>
        <table>
<?php
  for($i = 0; $i < count($_GET["files"]); $i++) {
    $file = $list[$_GET["files"][$i]];
    if (count($_GET['filestounrar'][$i]) == 0) { continue; }
?>
          <tr align="center">
            <td colspan="2" style="border-right:1px solid #666; border-left:1px solid #666; border-top:1px solid #666; padding:2px; background-color:#001825;"><?php printf(lang(373),basename($file["name"])); ?></td>
          </tr>
<?php
    foreach ($_GET['filestounrar'][$i] as $rar_item) {
?>
          <tr>
            <td class="unrar-lb-td">
<?php
      echo link_for_file(realpath($options['download_dir']).'/'.basename(base64_decode($rar_item)));
?>
            </td>
            <td id="<?php echo 'unrar'.$_GET["files"][$i].'-'.str_replace('=', '-', $rar_item); ?>" align="center" style="border-right:1px solid #666; padding:2px; background-color:#001825;"><?php echo lang(364); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2" class="unrar-space-td">&nbsp;</td>
          </tr>
<?php
  }
?>
        </table>
      </td>
    </tr>
    <tr>
      <td>
      </td>
    </tr>
  </table>
  <span id="unrar_finished" style="display:none;"><a href="<?php echo $PHP_SELF."?act=files"; ?>"><?php echo lang(365); ?></a><br /><br /><br /></span>
<?php
}




function unrar_go_go() {
  global $options, $list;
?>
<script type="text/javascript">
/* <![CDATA[ */
function rar_st(elementid, st){
  document.getElementById(elementid).innerHTML = st;
  return true;
}
/* ]]> */
</script>
<?php
  for($i = 0; $i < count($_GET["files"]); $i++) {
    $file = $list[$_GET["files"][$i]];
    if (count($_GET['filestounrar'][$i]) == 0) { continue; }
    foreach ($_GET['filestounrar'][$i] as $rar_item) {
      flush();
      $rar = new rlRar($file["name"], $options['check_these_before_unzipping'] ? $options['forbidden_filetypes'] : array('.xxx'));
      if ($rar->get_rar_return() === false) {
?>
<script type="text/javascript">rar_st('<?php echo 'unrar'.$_GET["files"][$i].'-'.str_replace('=', '-', $rar_item); ?>', '<?php echo lang(343); ?>');</script>
<?php
      }
      else {
        $rar_result = $rar->extract(base64_decode($rar_item), $options['download_dir'], $_GET['passwords'][$i], 'unrar'.$_GET["files"][$i].'-'.str_replace('=', '-', $rar_item), $i);
        echo $rar_result;
        if (strpos($rar_result, ", 'OK')") !== false) {
          _create_list();
          $rar_tolist = realpath($options['download_dir']).'/'.basename(base64_decode($rar_item));
          $time = filemtime($rar_tolist); while (isset($list[$time])) { $time++; }
          $list[$time] = array("name" => $rar_tolist, "size" => bytesToKbOrMbOrGb(filesize($rar_tolist)), "date" => $time);
          if (!updateListInFile($list)) {
?>
<script type="text/javascript">var tmp = document.getElementById('rar_finished'); tmp.innerHTML = "<?php echo lang(9); ?><br /><br />" + tmp.innerHTML</script>;
<?php
          }
        }
      }
    }
  }
?>
<script type="text/javascript">document.getElementById('unrar_finished').style.display = '';</script>
<?php
}
?>