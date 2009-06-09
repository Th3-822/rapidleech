<?php
function rl_md5() {
	global $list;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select atleast one file.<br><br>";
	} else {
?>
                         <table align="center" border=0
			cellspacing="2" cellpadding="4">
			<tr bgcolor="#243E4A">
				<td align=center>File
				
				
				<td align=center>Size
				
				
				<td align=center>MD5
			
			</tr>
<?php
		
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [($_GET ["files"] [$i])];
			
			if (file_exists ( $file ["name"] )) {
?>
                                                <tr bgcolor="#2B3C43">
				<td nowrap="nowrap"><b>&nbsp;<?php echo basename ( $file ["name"] ) . "</b><td align=center>&nbsp;" . $file ["size"]?>&nbsp;</td>
				<td nowrap="nowrap"><b>&nbsp;<font
					style="font-family: monospace; color: #FFA300"><?php echo md5_file ( $file ["name"] )?></font>&nbsp;</b>			
			</tr>
<?php
			}
		}
?>
                          </table>
		<br>
<?php
	}
}
?>