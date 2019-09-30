<?php

require_once('rl_init.php');
if ($options['auto_download_disable']) {
	require_once('deny.php');
	exit();
}
error_reporting(0);
ignore_user_abort(true);

login_check();

require(TEMPLATE_DIR . '/header.php');
?>
<br />
<center>
<?php
if (isset($_REQUEST['GO']) && $_REQUEST['GO'] == 'GO') {
	$_REQUEST['links'] = (isset($_REQUEST['links'])) ? trim($_REQUEST['links']) : '';
	if (empty($_REQUEST['links'])) html_error('No link submited');
	$getlinks = array_values(array_unique(array_filter(array_map('trim', explode("\r\n", $_REQUEST['links'])))));
	if (count($getlinks) < 1) html_error('No links submited');
	if (isset($_REQUEST['server_side']) && $_REQUEST['server_side'] == 'on') {
		// Get supported download plugins
		require_once(HOST_DIR . 'download/hosts.php');
		require_once(CLASS_DIR . 'ftp.php');
		require_once(CLASS_DIR . 'http.php');
?>
<table class="container" cellspacing="1">
	<tr>
		<td width="80%" align="center"><b><?php echo lang(21); ?></b></td>
		<td width="70" align="center"><b><?php echo lang(22); ?></b></td>
	</tr>
<?php
		for ($i = 0; $i < count($getlinks); $i++) echo "\t<tr><td width='80%' nowrap='nowrap'>".htmlentities($getlinks[$i])."</td><td width='70' id='status$i'>".lang(23)."</td></tr>$nn";
?>
</table>
<script type="text/javascript">
/* <![CDATA[ */
function updateStatus(id, status) {
	document.getElementById('status'+id).innerHTML = status;
}
function resetProgress() {
	document.getElementById('received').innerHTML = '0 KB';
	document.getElementById('percent').innerHTML = '0%';
	document.getElementById('progress').style.width = '0%';
	document.getElementById('speed').innerHTML = '0 KB/s';
	document.title = 'RAPIDLEECH PLUGMOD - Auto Download';
}
/* ]]> */
</script>
<br /><br />
<?php
		for ($i = 0; $i < count($getlinks); $i++) {
			$isHost = false;
			unset($FileName);
			unset($force_name);
			//$bytesReceived = 0; // fix for GLOBAL in geturl()
			unset($bytesReceived);

			$LINK = $getlinks[$i];
			$Url = parse_url($LINK);
			$Url['scheme'] = strtolower($Url['scheme']);
			$Url['path'] = (empty($Url['path'])) ? '/' :str_replace('%2F', '/', rawurlencode(rawurldecode($Url['path'])));

			$Referer = $Url;
			unset($Referer['user'], $Referer['pass']); // Remove login from Referer
			$Referer = rebuild_url($Referer);

			$_GET = array('GO' => 'GO'); // for insert_location()

			if (isset($_POST['useproxy']) && (empty($_POST['proxy']) || strpos($_POST['proxy'], ':') === false)) html_error(lang(20));
			if (isset($_POST['useproxy']) && $_POST['useproxy'] == 'on') {
				$_GET['useproxy'] = 'on';
				$_GET['proxy'] = $_POST['proxy'];
				$_GET['pauth'] = (!empty($_GET['proxyuser']) && !empty($_GET['proxypass'])) ? base64_encode($_GET['proxyuser'] . ':' . $_GET['proxypass']) : '';
			}

			if (isset($_POST['premium_acc'])) {
				$_GET['premium_acc'] = 'on';
				$_GET['premium_user'] = $_REQUEST['premium_user'] = (empty($_POST['premium_user'])) ? '' : $_POST['premium_user'];
				$_GET['premium_pass'] = $_REQUEST['premium_pass'] = (empty($_POST['premium_pass'])) ? '' : $_POST['premium_pass'];
			}

			if (!empty($Url['user']) && !empty($Url['pass'])) {
				$_GET['premium_acc'] = 'on';
				$_GET['premium_user'] = $_REQUEST['premium_user'] = $Url['user'];
				$_GET['premium_pass'] = $_REQUEST['premium_pass'] = $Url['pass'];
				$auth = urlencode(encrypt(base64_encode(rawurlencode($Url['user']) . ':' . rawurlencode($Url['pass']))));
				unset($Url['user'], $Url['pass']);
			} elseif (empty($Url['user']) xor empty($Url['pass'])) unset($Url['user'], $Url['pass']);

			$LINK = rebuild_url($Url);

			if (!in_array($Url['scheme'], array('http', 'https', 'ftp'))) echo "<script type='text/javascript'>updateStatus($i, '".lang(24)."');</script>$nn";
			else {
				require_once(TEMPLATE_DIR . '/transloadui.php');
				echo "<div id='progress$i' style='display:block;'>$nn";
				$isHost = false;
				$redir = $lastError = '';
				$GLOBALS['throwRLErrors'] = true;
				foreach ($host as $site => $file) {
					if (host_matches($site, $Url['host'])) { //if (preg_match("/^(.+\.)?".$site."$/i", $Url['host'])) {
						$isHost = true;
						try {
							require_once(HOST_DIR . 'DownloadClass.php');
							require_once(HOST_DIR . 'download/' . $file);
							$class = substr($file, 0, -4);
							$firstchar = substr($file, 0, 1);
							if ($firstchar > 0) $class = "d$class";
							if (class_exists($class)) {
								$hostClass = new $class(false);
								$hostClass->Download($LINK);
							}
						} catch (Exception $e) {
							echo "</div><script type='text/javascript'>updateStatus($i, '".htmlspecialchars($e->getMessage(), ENT_QUOTES)."');$nn"."document.getElementById('progress$i').style.display='none';</script>$nn";
							continue 2;
						}
					}
				}
				if (!$isHost) {
					$FileName = isset($Url['path']) ? basename($Url['path']) : '';
					$redir = GetDefaultParams();
					$redir['filename'] = urlencode($FileName);
					$redir['host'] = urlencode($Url['host']);
					if (!empty($Url['port'])) $redir['port'] = urlencode($Url['port']);
					$redir['path'] = urlencode($Url['path'] . (!empty($Url['query']) ? '?' . $Url['query'] : ''));
					$redir['referer'] = urlencode($Referer);
					$redir['link'] = urlencode($LINK);
					if (!empty($_GET['cookie'])) $redir['cookie'] = urlencode(encrypt($_GET['cookie']));
					if (!empty($auth)) $redir['auth'] = $auth;
					insert_location($redir);
				}
				echo "<script type='text/javascript'>updateStatus($i, '".lang(25)."');</script>$nn";

				$_GET['saveto'] = ($options['download_dir_is_changeable'] ? urldecode(trim($_GET['saveto'])) : (substr($options['download_dir'], 0, 6) != 'ftp://') ? realpath(DOWNLOAD_DIR) : $options['download_dir']);
				$_GET['proxy'] = !empty($_GET['proxy']) ? trim(urldecode($_GET['proxy'])) : '';
				$pauth = (empty($_GET['proxy']) || empty($_GET['pauth'])) ? '' : urldecode(trim($_GET['pauth']));
				do {
					$_GET['filename'] = urldecode(trim($_GET['filename']));
					if (strpos($_GET['filename'], '?') !== false) $_GET['filename'] = substr($_GET['filename'], 0, strpos($_GET['filename'], '?'));
					$_GET['host'] = urldecode(trim($_GET['host']));
					$_GET['path'] = urldecode(trim($_GET['path']));
					$_GET['port'] = !empty($_GET['port']) ? urldecode(trim($_GET['port'])) : 0;
					$_GET['referer'] = !empty($_GET['referer']) ? urldecode(trim($_GET['referer'])) : 0;
					$_GET['link'] = urldecode(trim($_GET['link']));
					$_GET['post'] = !empty($_GET['post']) ? unserialize(decrypt(urldecode(trim($_GET['post'])))) : 0;
					$_GET['cookie'] = !empty($_GET['cookie']) ? decrypt(urldecode(trim($_GET['cookie']))) : '';
					$redirectto = '';

					$AUTH = array();
					$_GET['auth'] = !empty($_GET['auth']) ? trim($_GET['auth']) : '';
					if ($_GET['auth'] == '1') {
						if (!preg_match('|^(?:.+\.)?(.+\..+)$|i', $_GET['host'], $hostmatch)) html_error('No valid hostname found for authorisation!');
						$hostmatch = str_replace('.', '_', $hostmatch[1]);
						if (isset($premium_acc["$hostmatch"]) && is_array($premium_acc["$hostmatch"]) && !empty($premium_acc["$hostmatch"]['user']) && !empty($premium_acc["$hostmatch"]['pass'])) {
							$auth = base64_encode($premium_acc["$hostmatch"]['user'] . ':' . $premium_acc["$hostmatch"]['pass']);
						} else html_error('No usable premium account found for this download - please set one in accounts.php');
					} elseif (!empty($_GET['auth'])) {
						$auth = decrypt(urldecode($_GET['auth']));
						list($AUTH['user'], $AUTH['pass']) = array_map('rawurldecode', explode(':', base64_decode($auth), 2));
					} else $auth = false;

					$pathWithName = $_GET['saveto'] . PATH_SPLITTER . basename(urldecode($_GET['filename']));
					while (strpos($pathWithName, "\\\\") !== false) $pathWithName = str_replace("\\\\", "\\", $pathWithName);
					if (strpos($pathWithName, '?') !== false) $pathWithName = substr($pathWithName, 0, strpos($pathWithName, '?'));

					echo "<script type='text/javascript'>updateStatus($i, '".lang(26)."');</script>$nn";
					$url = parse_url($_GET['link']);
					if (empty($url['port'])) $url['port'] = $_GET['port'];
					if (isset($url['scheme']) && $url['scheme'] == 'ftp' && empty($_GET['proxy'])) $file = getftpurl($_GET['host'], defport($url), urldecode($_GET['path']), $pathWithName);
					else {
						!empty($_GET['force_name']) ? $force_name = urldecode($_GET['force_name']) : '';
						$file = geturl($_GET['host'], defport($url), $_GET['path'], $_GET['referer'], $_GET['cookie'], $_GET['post'], $pathWithName, $_GET['proxy'], $pauth, $auth, $url['scheme']);
					}

					if ($options['redir'] && $lastError && strpos($lastError, substr(lang(95), 0, strpos(lang(95), '%1$s'))) !== false) {
						$redirectto = trim(cut_str($lastError, substr(lang(95), 0, strpos(lang(95), '%1$s')), ']'));
						$_GET['referer'] = urlencode($_GET['link']);
						if (strpos($redirectto, '://') === false) { // If redirect doesn't have the host
							$ref = parse_url(urldecode($_GET['referer']));
							unset($ref['user'], $ref['pass'], $ref['query'], $ref['fragment']);
							if (substr($redirectto, 0, 1) != '/') $redirectto = "/$redirectto";
							$purl = array_merge($ref, parse_url($redirectto));
						} else $purl = parse_url($redirectto);
						$_GET['link'] = urlencode(rebuild_url($purl));
						$_GET['filename'] = urlencode(basename($purl['path']));
						$_GET['host'] = urlencode($purl['host']);
						$_GET['path'] = urlencode($purl['path'] . (!empty($purl['query']) ? '?' . $purl['query'] : ''));
						$_GET['port'] = !empty($purl['port']) ? $purl['port'] : 80;
						$_GET['cookie'] = !empty($_GET['cookie']) ? urlencode(encrypt($_GET['cookie'])) : '';
						if (is_array($_GET['post'])) $_GET['post'] = urlencode(encrypt(serialize($_GET['post'])));
						$lastError = $_GET['auth'] = ''; // With $_GET['auth'] empty it will still using the $auth
						unset($ref, $purl);
					}
					if ($lastError) echo "<script type='text/javascript'>updateStatus($i, '".addslashes($lastError)."');</script>$nn";
					elseif ($file['bytesReceived'] == $file['bytesTotal'] || $file['size'] == 'Unknown') {
						echo "<script type='text/javascript'>updateStatus($i, '100%');</script>$nn";
						write_file(CONFIG_DIR."files.lst", serialize(array('name' => $file['file'], 'size' => $file['size'], 'date' => time(), 'link' => $_GET['link'], 'comment' => (!empty($_GET['comment']) ? str_replace(array("\r", "\n"), array('\r', '\n'), $_GET['comment']) : ''))) . "\r\n", 0);
					} else echo "<script type='text/javascript'>updateStatus($i, '".lang(27)."');</script>$nn";
				} while ($redirectto && !$lastError);
				echo "</div>$nn<script type='text/javascript'>resetProgress();document.getElementById('progress$i').style.display='none';</script>$nn";
			}
			if (isset($_POST['server_dodelay']) && $_POST['server_dodelay'] == 'on' && !empty($_POST['serversidedelay'])) sleep((int) $_POST['serversidedelay']);
		}
		echo "<script type='text/javascript'>$('.transloadui').hide();</script>$nn";
		exit;
	} else {
		$start_link = 'index.php?audl=doum';

		if (isset($_REQUEST['useproxy']) && (empty($_REQUEST['proxy']) || strpos($_REQUEST['proxy'], ':') === false)) html_error(lang(20));
		elseif (isset($_REQUEST['useproxy']) && $_REQUEST['useproxy'] == 'on') {
			$start_link .= '&useproxy=on&proxy=' . urlencode(trim($_REQUEST['proxy']));
			if (!empty($_REQUEST['proxyuser']) && !empty($_REQUEST['proxypass'])) {
				$start_link .= '&proxyuser=' . urlencode(trim($_REQUEST['proxyuser']));
				$start_link .= '&proxypass=' . urlencode(trim($_REQUEST['proxypass']));
			}
		}

		if (!empty($_POST['premium_acc']) && $_POST['premium_acc'] == 'on') {
			$start_link .= '&premium_acc=on';
			if (!empty($_POST['premium_user']) && !empty($_POST['premium_pass'])) $start_link .= '&premium_user=' . urlencode(trim($_POST['premium_user'])) . '&premium_pass='.urlencode(trim($_POST['premium_pass']));
		}

		if (isset($_POST['cookieuse']) && !empty($_POST['cookie'])) $start_link .= '&cookie=' . urlencode(trim($_POST['cookie']));
		if (isset($_POST['ytube_mp4']) && isset($_POST['yt_fmt'])) $start_link .= '&ytube_mp4=' . urlencode($_POST['ytube_mp4']) . '&yt_fmt='.urlencode($_POST['yt_fmt']);
		if (isset($_POST['cleanname'])) $start_link .= '&cleanname=' . urlencode($_POST['cleanname']);

?>
<script type="text/javascript">
/* <![CDATA[ */
	var current_dlink = -1;
	var links = new Array();
	var start_link = '<?php echo $start_link; ?>';

	function startauto() {
		current_dlink = -1;
		document.getElementById('auto').style.display = 'none';
		nextlink();
	}

	function nextlink() {
		if (document.getElementById('status'+current_dlink)) document.getElementById('status'+current_dlink).innerHTML = '<?php echo lang(28); ?>';
		current_dlink++;

		if (current_dlink < links.length) {
			document.getElementById('status'+current_dlink).innerHTML = '<?php echo lang(26); ?>';
			opennewwindow(current_dlink);
		}
	}

	function opennewwindow(id) {
		window.frames['idownload'].location = start_link+'&link='+links[id];
	}

	function addLinks() {
		var tbody = document.getElementById('links').getElementsByTagName('tbody')[0];
		var stringLinks = document.getElementById('addlinks').value;
		var regexRN = new RegExp('\r\n', 'g');
		var regexN = new RegExp('\n', 'g');
		var stringLinksN = stringLinks.replace(regexRN, "\n");
		var arrayLinks = stringLinksN.split(regexN);
		for (var i = 0; i < arrayLinks.length; i++) {
			var row = document.createElement('tr');
			var td1 = document.createElement('td');
			td1.appendChild(document.createTextNode(arrayLinks[i]));
			var td2 = document.createElement('td');
			td2.appendChild(document.createTextNode('<?php echo lang(23); ?>'));
			td2.setAttribute('id', 'status'+links.length);
			row.appendChild(td1);
			row.appendChild(td2);
			tbody.appendChild(row);
			links[links.length] = arrayLinks[i];
		}
		document.getElementById('addlinks').value = '';
	}
<?php for ($i = 0; $i < count($getlinks); $i++) echo "\tlinks[$i] = '" . urlencode($getlinks[$i]) . "';\n"; ?>
/* ]]> */
</script>

<table id="links" class="container" cellspacing="1">
	<thead>
		<tr><td width="80%" align="left"><b><?php echo lang(21); ?></b></td><td width="70" align="left"><b><?php echo lang(22); ?></b></td></tr>
	</thead><tfoot>
		<tr id="auto"><td colspan="2" align="center"><input type="button" value="<?php echo lang(29); ?>" onclick="javascript:startauto();" /></td></tr>
	</tfoot><tbody>
<?php for ($i = 0; $i < count($getlinks); $i++) echo "\t<tr><td nowrap='nowrap'>".htmlentities($getlinks[$i])."</td><td id='status$i'>" . lang(307) . "</td></tr>\r\n"; ?>
	</tbody>
</table>
<br />
<iframe width="90%" height="300" src="" name="idownload"><?php echo lang(30); ?></iframe>
<br />
<table class="container" cellspacing="1">
	<tr>
		<td><textarea name="addlinks" id="addlinks" cols="100" rows="5"></textarea></td>
		<td><input type="button" value="<?php echo lang(31); ?>" onclick="javascript:addLinks();" /></td>
	</tr>
</table>
</center>
<?php
		include(TEMPLATE_DIR.'footer.php');
		exit;
	}
}
?>
<script type="text/javascript">
/* <![CDATA[ */
	function ViewPage(page) {
		document.getElementById('listing').style.display = 'none';
		document.getElementById('options').style.display = 'none';
		document.getElementById(page).style.display = 'block';
	}

	function HideAll() {
		document.getElementById('entered').style.display = 'none';
		/* document.getElementById('worked_frame').style.display = 'block'; */
	}
/* ]]> */
</script>
<table class="container" cellspacing="0" cellpadding="1" id="entered">
	<tr><td>
		<form action="?GO=GO" method="post" >
		<table align="center" width="700" border="0">
			<tr id="menu"><td width="700" align="center"><a href="javascript:ViewPage('listing');"><?php echo lang(32); ?></a>&nbsp;|&nbsp;<a href="javascript:ViewPage('options');"><?php echo lang(33); ?></a></td></tr>
			<tr><td width="100%" valign="top">
				<div id="listing" style="display:block;">
					<table border="0" style="width:710px;">
						<tr><td align="center"><textarea id="links" name="links" rows="25" cols="100" style="width:600px;height:400px;border:1px solid #002E43;"></textarea></td></tr>
						<tr><td align="center" valign="top"><input type="submit" value="<?php echo lang(34); ?>" onclick="javascript:HideAll();" style="width:100px;" /></td></tr>
					</table>
				</div>
				<div id="options" style="display:none;">
					<table cellspacing="5" style="width:710px;">
						<tbody>
							<tr><td align="center">
								<table align="center">
									<tr>
										<td><input type="checkbox" id="useproxy" name="useproxy" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('proxy').style.display=displ;"<?php echo !empty($_COOKIE['useproxy']) ? ' checked="checked"' : ''; ?> />&nbsp;<?php echo lang(35); ?></td>
										<td>&nbsp;</td>
										<td id="proxy"<?php echo !empty($_COOKIE['useproxy']) ? '' : ' style="display: none;"'; ?>>
											<table border="0">
												<tr><td><?php echo lang(36); ?>:</td><td><input name="proxy" size="25"<?php echo !empty($_COOKIE['roxy']) ? ' value="'.htmlspecialchars($_COOKIE['proxy'], ENT_QUOTES).'"' : ''; ?> /></td></tr>
												<tr><td><?php echo lang(37); ?>:</td><td><input name="proxyuser" size="25"<?php echo !empty($_COOKIE['proxyuser']) ? ' value="'.htmlspecialchars($_COOKIE['proxyuser'], ENT_QUOTES).'"' : ''; ?> /></td></tr>
												<tr><td><?php echo lang(38); ?>:</td><td><input name="proxypass" size="25"<?php echo !empty($_COOKIE['proxypass']) ? ' value="'.htmlspecialchars($_COOKIE['proxypass'], ENT_QUOTES).'"' : ''; ?> /></td></tr>
											</table>
										</td>
									</tr>
<?php if ($options['download_dir_is_changeable']) { ?>
									<tr>
										<td><input type="checkbox" name="saveto" id="saveto" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('path').style.display=displ;"<?php echo !empty($_COOKIE['saveto']) ? ' checked="checked"' : ''; ?> />&nbsp;<?php echo lang(40); ?></td>
										<td>&nbsp;</td>
										<td id="path" <?php echo !empty($_COOKIE['saveto']) ? '' : ' style="display: none;"'; ?>><?php echo lang(41); ?>:&nbsp;<input name="savedir" size="30" value="<?php echo (!empty($_COOKIE['savedir']) ? $_COOKIE['savedir'] : (substr($options['download_dir'], 0, 6) != 'ftp://' ? realpath(DOWNLOAD_DIR) : $options['download_dir'])); ?>" /></td>
									</tr>
<?php } ?>
									<tr>
										<td><input type="checkbox" value="on" name="premium_acc" id="premium_acc" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('premiumblock').style.display=displ;"<?php if (count($premium_acc) > 0) echo ' checked="checked"'; ?> />&nbsp;<?php echo lang(42); ?></td>
										<td>&nbsp;</td>
										<td id="premiumblock" style="display: none;">
											<table width="150" border="0">
												<tr><td><?php echo lang(37); ?>:&nbsp;</td><td><input type="text" name="premium_user" id="premium_user" size="15" value="" /></td></tr>
												<tr><td><?php echo lang(38); ?>:&nbsp;</td><td><input type="password" name="premium_pass" id="premium_pass" size="15" value="" /></td></tr>
											</table>
										</td>
									</tr>
									<tr>
										<td><input type="checkbox" name="cookieuse" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('cookieblock').style.display=displ;" />&nbsp;<?php echo lang(235); ?></td>
										<td>&nbsp;</td>
										<td id="cookieblock" style="display: none;">
											<table width="150" border="0">
												<tr><td><?php echo lang(236); ?>;</td><td><input type="text" name="cookie" id="cookie" size="25" value="" /></td></tr>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<label><input type="checkbox" name="ytube_mp4" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('ytubeopt').style.display=displ;" checked="checked" />&nbsp;<?php echo lang(206); ?></label>
											<table width="150" border="0" id="ytubeopt" style="display: none;">
												<tr>
													<td>&nbsp;<label><input type="checkbox" name="cleanname" checked="checked" value="1" /><small>&nbsp;Remove non-supported characters from filename</small></label></td>
												</tr>
												<tr>
													<td>
														<select name="yt_fmt" id="yt_fmt">
															<option value="highest" selected="selected"><?php echo lang(219); ?></option>
															<option value='22'>[22] Video: MP4 720p | Audio: AAC ~192 Kbps</option>
															<option value='43'>[43] Video: WebM 360p | Audio: Vorbis ~128 Kbps</option>
															<option value='18'>[18] Video: MP4 360p | Audio: AAC ~96 Kbps</option>
															<option value='5'>[5] Video: FLV 240p | Audio: MP3 ~64 Kbps</option>
															<option value='36'>[36] Video: 3GP 240p | Audio: AAC ~36 Kbps</option>
															<option value='17'>[17] Video: 3GP 144p | Audio: AAC ~24 Kbps</option>
															<option value='138'>[138] Video only: MP4 @ 4320p</option>
															<option value='272'>[272] Video only: WebM @ 4320p</option>
															<option value='315'>[315] Video only: WebM @ 2160p60</option>
															<option value='266'>[266] Video only: MP4 @ 2160p</option>
															<option value='313'>[313] Video only: WebM @ 2160p</option>
															<option value='308'>[308] Video only: WebM @ 1440p60</option>
															<option value='264'>[264] Video only: MP4 @ 1440p</option>
															<option value='271'>[271] Video only: WebM @ 1440p</option>
															<option value='299'>[299] Video only: MP4 @ 1080p60</option>
															<option value='303'>[303] Video only: WebM @ 1080p60</option>
															<option value='137'>[137] Video only: MP4 @ 1080p</option>
															<option value='248'>[248] Video only: WebM @ 1080p</option>
															<option value='298'>[298] Video only: MP4 @ 720p60</option>
															<option value='302'>[302] Video only: WebM @ 720p60</option>
															<option value='140'>[140] Audio only: AAC @ ~128 Kbps</option>
															<option value='171'>[171] Audio only: Vorbis @ ~160 Kbps</option>
															<option value='251'>[251] Audio only: Opus @ ~128 Kbps</option>
															<option value='250'>[250] Audio only: Opus @ ~64 Kbps</option>
															<option value='249'>[249] Audio only: Opus @ ~48 Kbps</option>
														</select>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr><td><label><input type="checkbox" name="server_side" value="on" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('serverside').style.display=displ;" />&nbsp;<?php echo lang(43); ?></label></td></tr>
									<tr id="serverside" style="display: none;">
										<td><input type="checkbox" name="server_dodelay" value="on" onclick="javascript:var displ=this.checked?'':'none';document.getElementById('serverdelay').style.display=displ;" /><?php echo lang(44); ?></td>
										<td>&nbsp;</td>
										<td id="serverdelay" style="display: none;"><?php echo lang(45); ?>: <input type="text" name="serversidedelay" /></td>
									</tr>
								</table>
							</td></tr>
						</tbody>
					</table>
				</div>
			</td></tr>
		</table>
		</form>
	</td></tr>
</table>
</center>
<?php include(TEMPLATE_DIR.'footer.php'); ?>