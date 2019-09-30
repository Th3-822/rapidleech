<?php

class rlRar {
	protected $filename, $rar_forbidden, $password, $rar_opts, $rar_descriptorspec, $rar_return, $rar_error, $rar_list, $debug, $rar_exec, $rar_version, $old_rar = true;

	function __construct($filename, $forbidden_filetypes=array('.xxx')) {
		$this->debug = false;
		$this->filename = trim($filename);
		$this->rar_forbidden = (!empty($forbidden_filetypes) ? '-x*' . implode($forbidden_filetypes, ' -x*') : '');
		if (is_file(ROOT_DIR . '/rar/rar')) {
			$this->rar_exec = ROOT_DIR . '/rar/rar';
			$return = 'rar';
		} elseif (is_file(ROOT_DIR . '/rar/unrar')) {
			$this->rar_exec = ROOT_DIR . '/rar/unrar';
			$return = 'unrar';
		} else $return = false;
		$this->password = '';
		$this->rar_error = '';
		$this->rar_list = false;
		$this->rar_descriptorspec = array(
			0 => array('pipe', 'r'), // stdin is a pipe that the child will read from
			1 => array('pipe', 'w'), // stdout is a pipe that the child will write to
			2 => array('pipe', 'w') // stderr is a pipe to write to
		);
		$this->rar_return = $return;
	}

	function get_rar_return() {
		return $this->rar_return;
	}

	function get_filename() {
		return $this->filename;
	}

	function fix_pass($password = '') {
		//$password fix/check
		return ($password == '' ? '' : '-p' . escapeshellarg(stripslashes(($password))) . ' ');
	}

	function check_numeric_opt(&$val, $max, $min=0) {
		$tmp = floor(intval($val));
		if (!is_float($tmp) || $tmp > $max || $tmp < $min) return false;
		$val = $tmp;
		return true;
	}

	function addtoarchive($rar_opts, $dest_dir, $jsoutid='', $debug_id=0) {
		$return = '';
		$dest_dir = realpath($dest_dir) . '/';
		$this->check_numeric_opt($rar_opts['comp_lvl'], 5);
		$this->filename = $dest_dir.basename($this->filename);
		$rar_part_exists = false;
		if (!empty($rar_opts['vols'])) {
			$tmp = basename(strtolower($this->filename));
			if (substr(strtolower($tmp), -4) == '.rar') $tmp = substr($tmp, 0, -4);
			$tmp .= '.part';
			clearstatcache();
			$rar_dir = opendir(realpath($dest_dir) . '/');
			while (false !== ($rar_f_dd = readdir($rar_dir))) {
				$rar_f_dd = basename(strtolower($rar_f_dd));
				if ($tmp == substr($rar_f_dd, 0, strlen($tmp)) && is_numeric(substr($rar_f_dd, strlen($tmp), -4))) {
					$rar_part_exists = true;
					break;
				}
			}
			closedir($rar_dir);
		}
		if (basename($this->filename) != preg_replace('/[^\w\040\.\-]/i', '', basename($this->filename))) $return = 'INVALID_RAR_FILENAME';
		elseif (basename($this->filename) == '' || basename($this->filename) == '.rar') $return = 'BAD_RAR_FILENAME';
		elseif (!is_dir($dest_dir)) $return = 'DESTINATION_NOT_EXISTS';
		elseif (file_exists($this->filename)) $return = 'RAR_EXISTS';
		elseif ($rar_part_exists) $return = 'RAR_PART_EXISTS';
		elseif (!empty($rar_opts['vols']) && !empty($rar_opts['vols_s']) && (!$this->check_numeric_opt($rar_opts['vols_s'], 1024, 1) || !$this->check_numeric_opt($rar_opts['vol_sm'], 6))) {
			$return = 'INVALID_VOLUMES_OPTIONS'; //var_export($this->check_numeric_opt($rar_opts['vols_s'], 1024, 1));
		} elseif (!empty($rar_opts['rec_rec']) && !empty($rar_opts['rec_rec_s']) && !$this->check_numeric_opt($rar_opts['rec_rec_s'], 10, 1)) $return = 'INVALID_RECOVERYR_OPTIONS';
		else {
			$this->rar_opts = array(
				'comp_lvl' => 0, 'vols' => 0, 'vols_s' => 100, 'vols_sm' => 3, 'delete' => 0, 'solid' => 0, 'rec_rec' => 0,
				'rec_rec_s' => 10, 'test' => 0,'use_pass1' => 0, 'pass' => '', 'use_pass2' => 0, 'path_i' => 0, 'path_i_path' => ''
			);
			$this->rar_opts = array_merge($this->rar_opts, $rar_opts);
			$rar_file_list = '';
			foreach($this->rar_opts['filestorar'] as &$rar_value) {
				$rar_value = basename($GLOBALS['list'][$rar_value]['name']);
				if (empty($rar_value)) $return = 'BAD_FILE_NAME';
				elseif (!is_file($dest_dir . $rar_value)) $return = "FILE_NOT_EXISTS: $rar_value";
				else $rar_file_list .= escapeshellarg($dest_dir . $rar_value) . ' ';
				if ($return !== '') {
					if ($jsoutid !== '') return "<script type='text/javascript'>/* <![CDATA[ */rar_st('$jsoutid', '" . preg_replace("/\r?\n/", "\\n", addslashes($return)) . "')/* ]]> */</script>\r\n";
					return $return;
				}
			}
			if ($rar_file_list == '') {
				$return = 'NO_FILES_TO_ADD';
			} else {
				$rar_cmd = 'a -ierr -ep -o- ';
				$rar_cmd .= '-ma' . (empty($this->rar_opts['rar5']) ? '4' : '5') . ' ';
				$rar_cmd .= "-m{$this->rar_opts['comp_lvl']} ";
				if ($this->rar_opts['vols']) {
					$vols_sm = array(3 => 'm', 4 => 'M', 5 => 'g', 6 => 'G');
					$rar_cmd .= sprintf('-v%s%s ', max(1, $this->rar_opts['vols_s']), $vols_sm[max(3, min(6, $this->rar_opts['vols_sm']))]);
				}
				if ($this->rar_opts['delete']) $rar_cmd .= '-df ';
				$rar_cmd .= $this->rar_opts['solid'] ? '-s -ds ' : '-s- ';
				if ($this->rar_opts['rec_rec']) $rar_cmd .= "-rr{$this->rar_opts['rec_rec_s']} ";
				if ($this->rar_opts['test']) $rar_cmd .= '-t ';
				if ($this->rar_opts['use_pass1']) {
					$password = $this->fix_pass($this->rar_opts['pass']);
					if ($password != '') {
						$rar_cmd .= $password;
						if ($this->rar_opts['use_pass2']) $rar_cmd .= '-hp ';
					}
				}
				if ($this->rar_opts['path_i']) $rar_cmd .= '-ap' . escapeshellarg($this->rar_opts['path_i_path']) . ' ';
				$this->runit($rar_cmd, $rar_file_list, $jsoutid, $debug_id);
				if ($this->rar_return == 7 && !empty($this->rar_list) && strpos($this->rar_list[0], 'Unknown option: ma') !== false) {
					// Using older rar, so it doesn't support the -ma switches
					$rar_cmd = str_replace(array(' -ma5 ', ' -ma4 '), ' ', $rar_cmd);
					$this->runit($rar_cmd, $rar_file_list, $jsoutid, $debug_id + 1);
				}
				switch ($this->rar_return) {
					case 0:
						if (strtolower(substr(trim($this->rar_error), -4)) == 'done') $return = 'Done';
						break;
					case 3: $return = 'CRC_ERROR';break;
					case 4: $return = 'LOCKED ARCHIVE';break;
					case 5: $return = 'WRITE ERROR';break;
					case 6: $return = 'OPEN ERROR';break;
					case 7: $return = 'USER ERROR';break;
					case 8: $return = 'MEMORY ERROR';break;
					case 9: $return = 'CREATE ERROR';break;
					case 255: $return = 'USER BREAK';break;
					default: $return = nl2br("UNKNOWN_ERROR\n{$this->rar_return}\n" . htmlspecialchars($this->rar_error));break;
				}
			}
		}
		if ($jsoutid !== '') return "<script type='text/javascript'>/* <![CDATA[ */rar_st('$jsoutid', '" . preg_replace("/\r?\n/", "\\n", addslashes($return)) . "')/* ]]> */</script>\r\n";
		return $return;
	}

	function listthem($password = '', $dest_dir, $debug_id) {
		$dest_dir = realpath($dest_dir).'/';
		$this->filename = $dest_dir.basename($this->filename);
		$password = $this->fix_pass($password);
		$this->runit('v -v -c- ' . $password, '', '', $debug_id);
		if (!$this->old_rar && !$password && $this->rar_return == 255 && empty($this->rar_error)) {
			// Workaround for weird bug (rar5 ends before requesting pw on a full encrypted file)
			$this->runit('v -v -c- -p- ', '', '', $debug_id + 1);
			if ($this->rar_return != 11) {
				// Workaround for the same bug on a older than rar5 file
				foreach ($this->rar_list as $rar_line) if (strpos($rar_line, ', encrypted headers') !== false) $this->rar_return = 11;
			}
		}
		$rar_needs_pass = false;
		//if (strpos($this->rar_error, 'Enter password') === 0) return array( 0 => 'PASS', 'NEEDP' => true);
		if (!$this->old_rar && $this->rar_return == 11) return array( 0 => 'PASS', 'NEEDP' => true);
		else if (strpos($this->rar_error, 'Enter password') === 0) return array( 0 => 'PASS', 'NEEDP' => true);
		else if (trim($this->rar_error) !== '') return array( 0 => 'ERROR', 1 => $this->rar_return, 2 => $this->rar_error, 'NEEDP' => false);
		$rar_files = array();
		$rar_onlist = false;
		$rar_onfile = true;
		foreach ($this->rar_list as $rar_line) {
			if (strpos($rar_line, '---') === 0) {
				$rar_onlist = !$rar_onlist;
				continue;
			}
			if (!$rar_onlist) continue;
			if ($this->old_rar) {
				if ($rar_onfile) {
					$rar_tmp_name = substr($rar_line, 1);
					$rar_tmp_pass = substr($rar_line, 0, 1) == '*' ? true : false;
				} else {
					$rar_tmp_size = strtok(trim($rar_line), ' ');
					$rar_tmp = strtok(' '); $rar_tmp = strtok(' ');
					$rar_tmp = strtok(' '); $rar_tmp = strtok(' ');
					$rar_tmp = strtok(' ');
					if (strpos(strtolower($rar_tmp), 'd') === false) {
						$rar_files[$rar_tmp_name]['size'] = $rar_tmp_size;
						$rar_needs_pass = $rar_tmp_pass ? true : $rar_needs_pass;
					}
				}
				$rar_onfile = !$rar_onfile;
			} else {
				if (preg_match('/^(\*)?(?:\s+[\w\.]{3}(?!D)[\w\.]{4}|(?(1)|\s)[dl\-](?:[r\-][w\-][x\-]){3})\s+(\d+)\s+\d+\s+(?:\d+%|<--)\s+(?:[\w\-]{10}|[\w\-]{8})\s+[\d:]{4,5}\s+[\w\.]{8}\s+([^\r\n]+)$/', $rar_line, $attr)) {
					$rar_files[$attr[3]]['size'] = $attr[2];
					if (!$rar_needs_pass && !empty($attr[1])) $rar_needs_pass = true;
				}
			}
		}
		if (count($rar_files) == 0) return array( 0 => 'ERROR', 1 => $this->rar_return, 2 => $this->rar_error.' (Empty/bad/non RAR file)', 'NEEDP' => false);
		return array( 0 => 'LIST', 'NEEDP' => $rar_needs_pass, 2 => $rar_files);
	}

	function extract($file=false, $dest, $password='', $jsoutid='', $debug_id=0) {
		$dest = realpath($dest) . '/';
		if ($file === false) $return = 'BAD_FILENAME';
		elseif (dirname(realpath($this->filename)) !== dirname($dest.'safe')) $return = 'RAR_INCORRECT_LOCATION';
		elseif (!is_file($this->filename)) $return = 'RAR_NOT_EXISTS';
		elseif (!is_dir($dest)) $return = 'DESTINATION_NOT_EXISTS';
		elseif (file_exists($dest . basename($file))) $return = 'FILE_EXISTS';
		else {
			$password = $this->fix_pass($password);
			$this->runit("e -ierr -o- -c- -ts- $password", "\"$file\" \"$dest\"", $jsoutid, $debug_id);
			switch ($this->rar_return) {
				case 0:
					if (strtolower(substr(trim($this->rar_error), -6)) == 'all ok') $return = 'OK';
					break;
				case 1:
					if (strpos($this->rar_error, 'Enter password') !== false) $return = 'PASSWORD_NEEDED';
					break;
				case 3: $return = (strpos($this->rar_error, 'password incorrect ?') !== false ? 'PASSWORD_INCORRECT_?' : 'CRC_ERROR');break;
				case 4: $return = 'LOCKED ARCHIVE';break;
				case 5: $return = 'WRITE ERROR';break;
				case 6: $return = 'OPEN ERROR';break;
				case 7: $return = 'USER ERROR';break;
				case 8: $return = 'MEMORY ERROR';break;
				case 9: $return = 'CREATE ERROR';break;
				case 11: $return = 'INCORRECT PASSWORD';break;
				case 255: $return = 'USER BREAK';break;
				default: $return = nl2br("UNKNOWN_ERROR\n{$this->rar_return}\n" . htmlspecialchars($this->rar_error));break;
			}
		}
		if ($jsoutid !== '') return "<script type='text/javascript'>/* <![CDATA[ */rar_st('$jsoutid', '" . preg_replace("/\r?\n/", "\\n", addslashes($return)) . "')/* ]]> */</script>\r\n";
		return $return;
	}

	function runit($params, $extra = '', $jsoutid = '', $debug_id = 0) {
		$rar_process = proc_open("'{$this->rar_exec}' $params {$this->rar_forbidden} -- " . escapeshellarg($this->filename) . " $extra", $this->rar_descriptorspec, $rar_pipes);
		if (is_resource($rar_process)) {
			fclose($rar_pipes[0]);
			$this->rar_list = array();
			if ($jsoutid === '') {//don't read this pipe if we getting js extract percentage
				while (!feof($rar_pipes[1])) {
					$rar_tmp = rtrim(fgets($rar_pipes[1]));
					if (trim($rar_tmp) !== '') $this->rar_list[] = $rar_tmp;
				}
			} else {
				// Always get first line of output (helps for version check)
				while (empty($this->rar_list) && !feof($rar_pipes[2])) {
					if (($rar_tmp = trim(fgets($rar_pipes[2]))) !== '') $this->rar_list[0] = $rar_tmp;
				}
			}
			fclose($rar_pipes[1]);
			if (!empty($this->rar_list) && preg_match('/(?<=^UNRAR |^RAR )\d+\.\d\d?(?=\s)/', $this->rar_list[0], $version)) {
				$this->rar_version = $version[0];
				$this->old_rar = intval($this->rar_version) < 5;
			}

			if ($jsoutid !== '') {
				$this->rar_error = $this->rar_list[0] . "\n";
				$last = '';
				$len = 0;
				$pos_s = 0;
				$num_last = 0;
				$pos_last = 0;
				$pos_found_s = '';
				while (!feof($rar_pipes[2])) {
					$read = fread($rar_pipes[2], 8);
					$len += strlen($read);
					$this->rar_error .= $read;
					if ($len > ($pos_s + 1)) {
						foreach (array('Adding', 'Testing', 'Calculating', 'Creating') as $find) {
							if (($pos_s_ = strpos($this->rar_error, $find, $pos_s+1)) !== false) break;
						}
					} else $pos_s_ = false;
					if ($pos_s_ !== false) {
						$pos_s = $pos_s_;
						$pos_found_s = true;
					}
					$pos = strrpos($this->rar_error, '%', $pos_last + 1);
					if ($pos !== false && $pos_last !== $pos) {
						$pos_last = $pos;
						$num = trim(substr($this->rar_error, $pos - 3, 3));
						if ($num_last !== $num) {
							$num_last = $num;
							if ($pos_found_s === true) {
								$pos_found_s = trim(substr($this->rar_error, $pos_s, strpos($this->rar_error, '%', $pos_s) - $pos_s - 7));
								$pos_tmp[0] = strpos($pos_found_s, '...');
								if ($pos_tmp[0] !== false) {
									$pos_tmp[1] = strpos($pos_found_s, 'Creating archive');
									if ($pos_tmp[1] === false) $pos_tmp[1] = strpos($pos_found_s, 'Creating solid archive');
									if ($pos_tmp[1] === 0) {
										$pos_found_s = '[Adding] ' . trim(substr($pos_found_s, $pos_tmp[0] + 3));
									} else {
										$pos_tmp[1] = strpos($pos_found_s, 'Testing archive');
										if ($pos_tmp[1] === 0) $pos_found_s = '[Testing] ' . trim(substr($pos_found_s, $pos_tmp[0] + 3));
										$pos_tmp[0] = strpos($pos_found_s, 'Calculating');
										if ($pos_tmp[0] === 0) $pos_found_s = '[Hashing File]';
									}
								}
								$pos_tmp[0] = strpos($pos_found_s, 'Adding');
								if ($pos_tmp[0] === 0) $pos_found_s = '[Adding] ' . basename(substr($pos_found_s, 11));
								$pos_tmp[0] = strpos($pos_found_s, 'Testing');
								if ($pos_tmp[0] === 0) $pos_found_s = '[Testing] ' . basename(substr($pos_found_s, 12));
								$pos_tmp[0] = strpos($pos_found_s, 'Calculating');
								if ($pos_tmp[0] === 0) $pos_found_s = '[Hashing File]';
								$pos_found_s = str_replace(array("\r\n", "\n", "\r"), '' , $pos_found_s);
							}
							echo "<script type='text/javascript'>/* <![CDATA[ */rar_st('$jsoutid', '" . addslashes($pos_found_s) . " {$num_last}%')/* ]]> */</script>\r\n";
							flush();
							if ($num_last == 100) $pos_found_s = '';
						}
					}
				}
			} else {
				$this->rar_error = '';
				while (!feof($rar_pipes[2])) $this->rar_error .= fread($rar_pipes[2], 8192);
			}
			$this->rar_error = trim($this->rar_error);
			fclose($rar_pipes[2]);
			$this->rar_return = proc_close($rar_process);
			if (!empty($this->rar_error) && preg_match('/(?<=^UNRAR |^RAR )\d+\.\d\d?(?=\s)/', $this->rar_error, $version)) {
				$this->rar_version = $version[0];
				$this->old_rar = intval($this->rar_version) < 5;
			}
		} else {
			$this->rar_error = (function_exists('proc_open')) ? 'Error opening rar process' : 'proc_open() is disabled';
			$this->rar_return = -1;
		}
		if ($this->debug || ($this->rar_return != 0 && strpos($this->rar_error, 'Enter password') !== 0) || ($this->rar_list !== FALSE && strpos($this->rar_list[count($this->rar_list)-1], 'is not RAR archive') !== FALSE)) {
			echo "<div id='rar_debug$debug_id' style='padding:2px;position:absolute; top:" . ((intval($debug_id) * 24) + 2) . "px; left:10px; background:#082330; border:2px solid #666666; text-align: left;'><b>Plusrar Debug Window" . (!$this->debug ? '('.lang(340).'), ' : '') . "</b>&nbsp;&nbsp;&nbsp;&nbsp;<span onclick='javascript:$(\"#rar_debug_contents$debug_id\").toggle();'>(" . lang(341) . ")</span><div id='rar_debug_contents$debug_id' style='display: none'><br /><textarea cols='100' rows='20' id='rar_debug_text$debug_id' readonly='readonly'>";
			echo "===plusrar version===\r\n{$GLOBALS['plusrar_v']}\r\n";
			printf("===php_uname===\r\n%s %s %s %s\r\n", php_uname('s'), php_uname('r'), php_uname('v'), php_uname('m'));
			if (version_compare(PHP_VERSION, '5.4.0', '<')) echo "===safe_mode===\r\n" . (ini_get('safe_mode') ? 'on' : 'off') . "\r\n";
			if (!empty($this->rar_version)) echo "===rar version===\r\n{$this->rar_version}\r\n";
			echo "===rar executable info===\r\n" . (is_file($this->rar_exec) ? 'Name: "' . htmlspecialchars(basename($this->rar_exec)) . '", Size: ' . filesize($this->rar_exec) . ' bytes, MD5:' . md5_file($this->rar_exec) . ', chmod: ' . substr(sprintf('%o', fileperms($this->rar_exec)), -4) : 'rar executable doesn\'t exists') . "\r\n";
			echo "===Return code===\r\n{$this->rar_return}\r\n";
			echo "===Error===\r\n" . str_replace('', '', preg_replace('/\d{1,2}\%\{4}&nbsp;/', '', (str_replace(array(' ', '&nbsp;0% - '), array('&nbsp;', '&nbsp;0%'), $this->rar_error)))) . "\r\n";
			if (is_array($this->rar_list)) {
				echo "===List output===\r\n";
				foreach ($this->rar_list as $line) echo htmlspecialchars($line) . "\r\n";
			}
			echo "===Executed command===\r\n" . htmlspecialchars("'{$this->rar_exec}' {$params} {$this->rar_forbidden} -- " . escapeshellarg($this->filename) . " $extra");
			echo "</textarea><br /><input type='button' value='" . lang(375) . "' onclick='javascript:document.getElementById(\"rar_debug_text$debug_id\").focus();document.getElementById(\"rar_debug_text$debug_id\").select();' /><div class='dragzone' style='text-align: center;font-weight: bold'>&nbsp;<br />" . lang(342) . "<br />&nbsp;</div></div></div><script type='text/javascript'>/* <![CDATA[ */";
			echo 'var dragObject=null, mouseOffset=null, makeDraggable=function(item){if(!item)return false;item.onmousedown=function(ev){var el=ev.target||ev.srcElement;if(el.className!="dragzone"){return true}dragObject=this;mouseOffset=getMouseOffset(this,ev);return false};return true};getMouseOffset=function(t,e){e=e||window.event;var dP=getPosition(t);var mP=mouseCoords(e);return{x:mP.x-dP.x,y:mP.y-dP.y}};mouseCoords=function(e){if(e.pageX||e.pageY){return{x:e.pageX,y:e.pageY}}return{x:e.clientX+document.body.scrollLeft-document.body.clientLeft,y:e.clientY+document.body.scrollTop-document.body.clientTop}};getPosition=function(e){var l=0;var t=0;while(e.offsetParent){l+=e.offsetLeft;t+=e.offsetTop;e=e.offsetParent}l+=e.offsetLeft;t+=e.offsetTop;return{x:l,y:t}};mouseMove=function(e){e=e||window.event;var mP=mouseCoords(e);if(dragObject){dragObject.style.position="absolute";dragObject.style.top=mP.y-mouseOffset.y+"px";dragObject.style.left=mP.x-mouseOffset.x+"px";return false}};mouseUp=function(){dragObject=null};document.onmousemove=mouseMove;document.onmouseup=mouseUp;';
			echo "makeDraggable(document.getElementById('rar_debug$debug_id'));\n/* ]]> */</script>";
		}
	}
}