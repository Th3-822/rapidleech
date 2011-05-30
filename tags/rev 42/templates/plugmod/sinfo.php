<hr />
<table class="ss-cpu-table">
<tr>
<!-- Start Server Space Info -->
<td align="left" valign="top" class="ss-td-style"><span class="ss-span-style"><?php echo lang(275); ?>:</span><br />
<?php echo lang(276); ?> = <b><span id="inuse"><?php echo ZahlenFormatieren($belegt); ?></span></b>&nbsp;(<span id="inusepercent"><?php echo round($prozent_belegt,"2"); ?></span> %)<br />
<?php if (extension_loaded('gd') && function_exists('gd_info')) { ?>
<img src="<?php echo CLASS_DIR ?>bar.php?rating=<?php echo round($prozent_belegt,"2"); ?>" border="0" name="diskpercent" id="diskpercent" alt="" /><br />
<?php } ?>
<?php echo lang(277); ?> = <b><span id="freespace"><?php echo ZahlenFormatieren($frei); ?></span></b><br />
<?php echo lang(278); ?> = <b><span id="diskspace"><?php echo ZahlenFormatieren($insgesamt); ?></span></b>
</td>

<!-- End Server Space -->


<!-- Start CPU Info -->
<td align="left" valign="top" class="cpu-td"><span class="cpu-span">
<?php
	if ($cpu_string === -1) { echo lang(135).'</span><hr />'; }
	else { echo lang(279).':</span><br />'.$cpu_string; }
?>
<span class="cpu-clock-st-text"><?php echo lang(280); ?>:</span> &nbsp;&nbsp;&nbsp;<span class="cpu-clock-st-time"><span id="server"></span></span><br /><span id="clock"></span>
</td>
<!-- End CPU Info -->

</tr>
</table>
<script type="text/javascript"> 
//<![CDATA[
    function goforit(){
		setTimeout("getthedate()",1000);
		timeDiff('<?php echo date('Y'); ?>','<?php echo date('n'); ?>','<?php echo date('j'); ?>','<?php echo date('G'); ?>','<?php echo date('i'); ?>','<?php echo date('s'); ?>','dd-mm-yyyy');
    }
    $(document).ready(function() {
        goforit();
    })
//]]> 
</script>
