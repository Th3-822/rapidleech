<div id="<?php echo (!empty($id) ? $id : 'uploaddiv'); ?>">
<table align="center" cellspacing="0" cellpadding="0" class="uploadui" id="progressblock">
<tr>
<td></td>
<td>
<div class="progressouter">
<div style="width:298px">
<div id="progress" class="progressup"></div>
</div>
</div>
</td>
<td></td>
<tr>
<td align="right" id="received">0 KB</td>
<td align="center" id="percent">0%</td>
<td align="left" id="speed">0 KB/s</td>
</tr>
</table>
</div>
<script type="text/javascript">
function pr(percent, received, speed)
{
	document.getElementById("received").innerHTML = '<b>' + received + '</b>';
	document.getElementById("percent").innerHTML = '<b>' + percent + '%</b>';
	document.title='Uploading ' + percent + '% [' + orlink + ']';
	document.getElementById("progress").style.width = percent + '%';
	document.getElementById("speed").innerHTML = '<b>' + speed + ' KB/s</b>';
	return true;
}
function mail(str, field)
{
	document.getElementById("mailPart." + field).innerHTML = str;
	return true;
}
</script>