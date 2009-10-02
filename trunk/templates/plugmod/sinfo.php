<hr>
<table style="border-collapse: separate; border-spacing: 2px 2px;">
<tr>
<td align="left" valign="top" style="color:ccc"><span style="color:#FF8700"><?php echo lang(275); ?>:</span><br>
<?php echo lang(276); ?> = <b><span id="inuse"><?php echo ZahlenFormatieren($belegt); ?></span></b>&nbsp;(<span id="inusepercent"><?php echo round($prozent_belegt,"2"); ?></span> %)<br>
<img src="<?php echo CLASS_DIR ?>bar.php?rating=<?php echo round($prozent_belegt,"2"); ?>" border="0" name="diskpercent" id="diskpercent" alt=""><br>
<?php echo lang(277); ?> = <b><span id="freespace"><?php echo ZahlenFormatieren($frei); ?></span></b><br>
<?php echo lang(278); ?> = <b><span id="diskspace"><?php echo ZahlenFormatieren($insgesamt); ?></span></b></td>
<td align="left" valign="top" style="color:ccc"><span style="color:#FF8700"><?php echo lang(279); ?>:</span><br>
<?php echo $cpu_string; ?>
<span style="color:#FF8700"><?php echo lang(280); ?>:</span> &nbsp;&nbsp;&nbsp;<span style="color:#999"><span id="server"></span></span><br /><span id="clock"></span>
</td>
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
