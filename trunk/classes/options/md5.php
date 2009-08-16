<?php
function rl_md5() {
	global $list;
	if (count ( $_GET ["files"] ) < 1) {
		echo "Select atleast one file.<br><br>";
	} else {
?>
<table class="md5table" align="center" border=0 cellspacing="2" cellpadding="4">
<tr>
<th align='center'><?php echo lang(104); ?></th>
<th align='center'><?php echo lang(56); ?></th>
<th align='center'>MD5</th>
</tr>
<?php
		
		for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
			$file = $list [($_GET ["files"] [$i])];
			
			if (file_exists ( $file ["name"] )) {
?>
<tr>
<td nowrap="nowrap">&nbsp;<?php echo '<b>' . basename ( $file ["name"] ) . "</b></td><td align=center>&nbsp;" . $file ["size"]?>&nbsp;</td>
<td nowrap="nowrap"><b>&nbsp;<?php echo md5_file ( $file ["name"] )?>&nbsp;</b>
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