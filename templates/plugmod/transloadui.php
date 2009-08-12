<br>
<table cellspacing="0" cellpadding="0" style="FONT-FAMILY: Tahoma; FONT-SIZE: 11px;">
<tr>
<td></td>
<td>
<div style='border:1px solid royalblue; width:300px; height:10px;'>
<div id="progress" style='background-color:#FFFFFF; width:0%; height:10px;'>
</div>
</div>
</td>
<td></td>
<tr>
<tr>
<td align="left" id="received">0 KB</td>
<td align="center" id="percent">0%</td>
<td align="right" id="speed">0 KB/s</td>
</tr>
</table>
<br>
<div id="resume" align="center"></div>
<script type="text/javascript" language="javascript">
function pr(percent, received, speed){
	document.getElementById("received").innerHTML = '<b>' + received + '</b>';
	document.getElementById("percent").innerHTML = '<b>' + percent + '%</b>';
	document.getElementById("progress").style.width = percent + '%';
	document.getElementById("speed").innerHTML = '<b>' + speed + ' KB/s</b>';
	document.title = percent + '% Downloaded';
	return true;
	}

function mail(str, field) {
	document.getElementById("mailPart." + field).innerHTML = str;
	return true;
	}
</script>
<br>