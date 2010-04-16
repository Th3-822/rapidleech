<?php
function rl_md5() {
  global $list;
?>
<table class="md5table" align="center" border="0" cellspacing="2" cellpadding="4">
  <tr>
    <th align="center"><?php echo lang(104); ?></th>
    <th align="center"><?php echo lang(56); ?></th>
    <th align="center">MD5</th>
  </tr>
<?php
  foreach ($_GET["files"] as $k => $v) {
    $file = $list[$v];
    if (file_exists($file["name"])) {
?>
  <tr>
    <td nowrap="nowrap">&nbsp;<b><?php echo htmlentities(basename($file["name"])); ?></b></td>
    <td align="center">&nbsp;<?php echo $file['size']; ?>&nbsp;</td>
    <td nowrap="nowrap"><b>&nbsp;<?php echo md5_file($file['name'])?>&nbsp;</b></td>
  </tr>
<?php
    }
  }
?>
</table>
<br />
<?php
}
?>