<hr />
<center>
<table class="ss-cpu-table">
<tr>
<td valign="top" class="ss-td-style"><span class="ss-span-style"></span>
<?php echo lang(276); ?>:<b><span id="inuse"><?php echo ZahlenFormatieren($belegt); ?></span></b>&nbsp;<b>/ <span id="diskspace"><?php echo ZahlenFormatieren($insgesamt); ?></span></b><br />
<?php if (extension_loaded('gd') && function_exists('gd_info')) { ?>
<img src="<?php echo CLASS_DIR ?>bar.php?rating=<?php echo round($prozent_belegt,"2"); ?>" id="diskpercent" alt="" /><br />
<?php } ?>
</td>
<td valign="top" class="cpu-td"><span class="cpu-span">
<?php
if ($cpu_string === -1) { "getCpuUsage(): couldn\'t access STAT path or STAT file invalid".'</span><hr />'; }
else { echo '</span>'.$cpu_string; }
?>
</td>
</tr>
</table>
</center>