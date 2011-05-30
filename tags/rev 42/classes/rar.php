<?php 

class rlRar {
  var $filename;
  var $rar_forbidden;
  var $password;
  var $rar_opts;
  var $rar_descriptorspec; 
  var $rar_return;
  var $rar_error;
  var $rar_list;
  var $debug;
  var $rar_exec;

  function rlRar($filename, $forbidden_filetypes=array('.xxx')) {
    return $this->__construct($filename, $forbidden_filetypes);
  }
  function __construct($filename, $forbidden_filetypes=array('.xxx')) {
    $this->debug = false;
    $this->filename = trim($filename);
    $this->rar_forbidden = '-x*'.implode($forbidden_filetypes, ' -x*');
    if (is_file(ROOT_DIR.'/rar/rar')) { $this->rar_exec = ROOT_DIR.'/rar/rar'; $return = 'rar'; } 
    elseif (is_file(ROOT_DIR.'/rar/unrar')) { $this->rar_exec = ROOT_DIR.'/rar/unrar'; $return = 'unrar'; }
    else { $return = false; }
    $this->password = '';
    $this->rar_error = '';
    $this->rar_list = FALSE;
    $this->rar_descriptorspec = array(
      0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
      1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
      2 => array("pipe", "w") // stderr is a pipe to write to
    );
    $this->rar_return = $return;
  }

  function fix_pass($password = '') {
//$password fix/check
    $password = ($password == '') ? '' : '-p'.escapeshellarg(stripslashes(($password))).' ';
    return $password;
  }

  function check_numeric_opt(&$val, $max, $min=0) {
    $tmp = floor(intval($val));
    if (!is_float($tmp) || $tmp > $max || $tmp < $min) { return false; }
    $val = $tmp;
    return true;
  }
  
  function addtoarchive($rar_opts, $dest_dir, $jsoutid='', $debug_id=0) {
    $return = '';
    $dest_dir = realpath($dest_dir).'/';
    $this->check_numeric_opt($rar_opts['comp_lvl'], 5);
    $this->filename = $dest_dir.basename($this->filename);
    $rar_part_exists = false;
    if ($rar_opts['vols']) {
      $tmp = basename(strtolower($this->filename));
      if (substr(strtolower($tmp), -4) == '.rar') { $tmp = substr($tmp, 0, -4); }
      $tmp .= '.part';
      clearstatcache();
      $rar_dir = opendir(realpath($dest_dir).'/');
      while (false !== ($rar_f_dd = readdir($rar_dir))) {
        $rar_f_dd = basename(strtolower($rar_f_dd));
        if ($tmp == substr($rar_f_dd, 0, strlen($tmp)) && is_numeric(substr($rar_f_dd, strlen($tmp), -4))) {
          $rar_part_exists = true;
          break;
        }
      }
      closedir($rar_dir);
    }
    if (basename($this->filename) != preg_replace("/[^a-z0-9\\040\\.\\-\\_]/i", '', basename($this->filename))) { $return = 'INVALID_RAR_FILENAME'; }
    elseif (basename($this->filename) == '' || basename($this->filename) == '.rar') { $return = 'BAD_RAR_FILENAME'; }
    elseif (!is_dir($dest_dir)) { $return = 'DESTINATION_NOT_EXISTS'; }
    elseif (file_exists($this->filename)) { $return = 'RAR_EXISTS'; }
    elseif ($rar_part_exists) { $return = 'RAR_PART_EXISTS'; }
    elseif ($rar_opts['vols'] && (!$this->check_numeric_opt($rar_opts['vols_s'], 1024, 1) || !$this->check_numeric_opt($rar_opts['vol_sm'], 6))) { $return = 'INVALID_VOLUMES_OPTIONS'; var_export($this->check_numeric_opt($rar_opts['vols_s'], 1024, 1)); }
    elseif ($rar_opts['rec_rec'] && !$this->check_numeric_opt($rar_opts['rec_rec_s'], 10, 1)) { $return = 'INVALID_RECOVERYR_OPTIONS'; }
    else {
      $this->rar_opts = array (
        'comp_lvl' => 0, 'vols' => 0, 'vols_s' => 100, 'vols_sm' => 3, 'delete' => 0, 'solid' => 0, 'rec_rec' => 0,
        'rec_rec_s' => 10, 'test' => 0,'use_pass1' => 0, 'pass' => '', 'use_pass2' => 0, 'path_i' => 0, 'path_i_path' => ''
      );
      $this->rar_opts = array_merge($this->rar_opts, $rar_opts);
      $rar_file_list = '';
      foreach($this->rar_opts['filestorar'] as &$rar_value) {
        $rar_value = basename($GLOBALS['list'][$rar_value]['name']);
        if (empty($rar_value)) { $return = 'BAD_FILE_NAME'; }
        elseif (!is_file($dest_dir.$rar_value)) { $return = 'FILE_NOT_EXISTS:'.$rar_value; }
        else { $rar_file_list .= escapeshellarg($dest_dir.$rar_value).' '; } 
        if ($return !== '') {
          if ($jsoutid !== '') { return '<script type="text/javascript">'."rar_st('".$jsoutid."', '".preg_replace("/\r?\n/", "\\n", addslashes($return))."')</script>\r\n"; }
          return $return;
        }
      }
      if ($rar_file_list == '') { $return = 'NO_FILES_TO_ADD'; }
      else {
        $rar_cmd = 'a -ierr -ep -o- ';
        $rar_cmd .= '-m'.$this->rar_opts['comp_lvl'].' ';
        if ($this->rar_opts['vols']) {
          $vols_sm = array(0=>'b', 1=>'k', 2=>'K', 3=>'m', 4=>'M', 5=>'g', 6=>'G');
          $rar_cmd .= '-v'.$this->rar_opts['vols_s'].$vols_sm[$this->rar_opts['vols_sm']].' ';
        }
        if ($this->rar_opts['delete']) { $rar_cmd .= '-df '; }
        $rar_cmd .= $this->rar_opts['solid'] ? '-s -ds ' : '-s- '; 
        if ($this->rar_opts['rec_rec']) { $rar_cmd .= '-rr'.$this->rar_opts['rec_rec_s'].' '; }
        if ($this->rar_opts['test']) { $rar_cmd .= '-t '; }
        if ($this->rar_opts['use_pass1']) { $password = $this->fix_pass($this->rar_opts['pass']); if ($password != '') {
          $rar_cmd .= $password;
          if ($this->rar_opts['use_pass2']) { $rar_cmd .= '-hp '; }
        } }
        if ($this->rar_opts['path_i']) { $rar_cmd .= '-ap'.escapeshellarg($this->rar_opts['path_i_path']).' '; }
        $this->runit($rar_cmd, $rar_file_list, $jsoutid, $debug_id);
        $return = nl2br("UNKNOWN_ERROR\n".$this->rar_return."\n".htmlentities($this->rar_error));
        if ($this->rar_return == 0) {
          if (strtolower(substr(trim($this->rar_error), -4)) == 'done') { $return = 'Done'; }
        }
        elseif ($this->rar_return == 255) { $return = "USER BREAK"; }
        elseif ($this->rar_return == 9) { $return = "CREATE ERROR"; }
        elseif ($this->rar_return == 8) { $return = "MEMORY ERROR"; }
        elseif ($this->rar_return == 7) { $return = "USER ERROR"; }
        elseif ($this->rar_return == 6) { $return = "OPEN ERROR"; }
        elseif ($this->rar_return == 5) { $return = "WRITE ERROR"; }
        elseif ($this->rar_return == 4) { $return = "LOCKED ARCHIVE"; }
        elseif ($this->rar_return == 3) { $return = "CRC ERROR"; }
      }
    }
    if ($jsoutid !== '') { return '<script type="text/javascript">/* <![CDATA[ */'."rar_st('".$jsoutid."', '".preg_replace("/\r?\n/", "\\n", addslashes($return))."')/* ]]> */</script>\r\n"; }
    return $return;
  }

  function listthem($password = '', $dest_dir, $debug_id) {
    $dest_dir = realpath($dest_dir).'/';
    $this->filename = $dest_dir.basename($this->filename);
    $password = $this->fix_pass($password);
    $this->runit('v -v -c- '.$password, '', '', $debug_id);
    $rar_needs_pass = false;
    if (strpos($this->rar_error, 'Enter password') === 0) { return array( 0 => 'PASS', 'NEEDP' => true); }
    if (trim($this->rar_error) !== '') { return array( 0 => 'ERROR', 1 => $this->rar_return, 2 => $this->rar_error, 'NEEDP' => false); }
    $rar_files = array();
    $rar_onlist = false;
    $rar_onfile = true;
    foreach ($this->rar_list as $rar_line) {
      if (strpos($rar_line, "---") === 0) { $rar_onlist = !$rar_onlist; continue; }
      if (!$rar_onlist) { continue; }
      if ($rar_onfile) {
       $rar_tmp_name = substr($rar_line, 1);
       $rar_tmp_pass = substr($rar_line, 0, 1) == '*' ? true : false;
      }
      else {
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
    }
    if ((count($rar_files) == 0)) { return array( 0 => 'ERROR', 1 => $this->rar_return, 2 => $this->rar_error.' (Empty/bad/non RAR file)', 'NEEDP' => false); }
    return array( 0 => 'LIST', 'NEEDP' => $rar_needs_pass, 2 => $rar_files);
  }

  function extract($file=false, $dest, $password='', $jsoutid='', $debug_id=0) {
    $dest = realpath($dest).'/';
    if ($file === false) { $return = 'BAD_FILENAME'; }
    elseif (dirname(realpath($this->filename)) !== dirname($dest.'safe')) { $return = 'RAR_INCORRECT_LOCATION'; }
    elseif (!is_file($this->filename)) { $return = 'RAR_NOT_EXISTS'; }
    elseif (!is_dir($dest)) { $return = 'DESTINATION_NOT_EXISTS'; }
    elseif (file_exists($dest.basename($file))) { $return = 'FILE_EXISTS'; }
    else {
      $password = $this->fix_pass($password);
      $this->runit('e -ierr -o- -c- '.$password, '"'.$file.'" "'.$dest.'"', $jsoutid, $debug_id);
      $return = nl2br("UNKNOWN_ERROR\n".$this->rar_return."\n".htmlentities($this->rar_error));
      if ($this->rar_return == 0) {
        if (strtolower(substr(trim($this->rar_error), -6)) == 'all ok') { $return = 'OK'; }
      }
      elseif ($this->rar_return == 1) {
        if (strpos($this->rar_error, 'Enter password') !== false) { $return = 'PASSWORD_NEEDED'; } 
      }
      elseif ($this->rar_return == 3) {
        if (strpos($this->rar_error, 'password incorrect ?') !== false) { $return = 'PASSWORD_INCORRECT_?'; }
        $return = "CRC_ERROR";
      }
      elseif ($this->rar_return == 255) { $return = "USER BREAK"; }
      elseif ($this->rar_return == 9) { $return = "CREATE ERROR"; }
      elseif ($this->rar_return == 8) { $return = "MEMORY ERROR"; }
      elseif ($this->rar_return == 7) { $return = "USER ERROR"; }
      elseif ($this->rar_return == 6) { $return = "OPEN ERROR"; }
      elseif ($this->rar_return == 5) { $return = "WRITE ERROR"; }
      elseif ($this->rar_return == 4) { $return = "LOCKED ARCHIVE"; }
    }
    if ($jsoutid !== '') { return '<script type="text/javascript">/* <![CDATA[ */'."rar_st('".$jsoutid."', '".preg_replace("/\r?\n/", "\\n", addslashes($return))."')/* ]]> */</script>\r\n"; }
    return $return;
  }

  function runit($params, $extra = '', $jsoutid = '', $debug_id = '') {
    $rar_process = proc_open("'".$this->rar_exec."' ".$params.' '.$this->rar_forbidden.' -- '.escapeshellarg($this->filename).' '.$extra, $this->rar_descriptorspec, $rar_pipes);
    if (is_resource($rar_process)) {
      fclose($rar_pipes[0]);
      $this->rar_list = array();
      if ($jsoutid == '') {//don't read this pipe if we getting js extract percentage
        while (!feof($rar_pipes[1])) { 
          $rar_tmp = rtrim(fgets($rar_pipes[1]));
          if (trim($rar_tmp) !== "") { $this->rar_list[] = $rar_tmp; }
        }
      }
      fclose($rar_pipes[1]);
      if ($jsoutid !== '') {
        $this->rar_error = '';
        $last = '';
        $pos_s = 0;
        $pos_last = 0;
        $pos_found_s = '';
        while (!feof($rar_pipes[2])) {
          $this->rar_error .= fgets($rar_pipes[2], 8);
          $pos_s_ = strpos($this->rar_error, 'Adding', $pos_s+1);
          if ($pos_s_ === false) { $pos_s_ = strpos($this->rar_error, 'Testing', $pos_s+1); }
          if ($pos_s_ === false) { $pos_s_ = strpos($this->rar_error, 'Calculating the control sum', $pos_s+1); }
          if ($pos_s_ === false) { $pos_s_ = strpos($this->rar_error, 'Testing archive', $pos_s+1); }
          if ($pos_s_ === false) { $pos_s_ = strpos($this->rar_error, 'Creating archive', $pos_s+1); }
          if ($pos_s_ !== false) { $pos_s = $pos_s_; $pos_found_s = true; }
          $pos = strrpos($this->rar_error, "%", $pos_last + 1);
          if ($pos !== false && $pos_last !== $pos) {
            $pos_last = $pos;
            $num = trim(substr($this->rar_error, $pos - 3, 3));
            if ($num_last !== $num) {
              $num_last = $num;
              if ($pos_found_s === true) {
                $pos_found_s = trim(substr($this->rar_error, $pos_s, strpos($this->rar_error, "%", $pos_s) - $pos_s - 7));
                $pos_tmp[0] = strpos($pos_found_s, '...');
                if ($pos_tmp[0] !== false) {
                  $pos_tmp[1] = strpos($pos_found_s, 'Creating archive');
                  if ($pos_tmp[1] === 0) { $pos_found_s = 'Adding     '.trim(substr($pos_found_s, $pos_tmp[0] + 3)); }
                  else {
                    $pos_tmp[1] = strpos($pos_found_s, 'Testing archive');
                    if ($pos_tmp[1] === 0) { $pos_found_s = 'Testing     '.trim(substr($pos_found_s, $pos_tmp[0] + 3)); }
                  }
                }
                $pos_tmp[0] = strpos($pos_found_s, 'Adding');
                if ($pos_tmp[0] === 0) { $pos_found_s = 'Adding     '.basename(substr($pos_found_s, 11)); }
                $pos_tmp[0] = strpos($pos_found_s, 'Testing');
                if ($pos_tmp[0] === 0) { $pos_found_s = 'Testing     '.basename(substr($pos_found_s, 12)); }
                $pos_found_s = str_replace(array("\r\n", "\n", "\r"), '' , $pos_found_s);
              }
              echo '<script type="text/javascript">'."rar_st('".$jsoutid."', '".addslashes($pos_found_s).' '.$num_last."%')</script>\r\n";
              flush();
              if ($num_last == 100) { $pos_found_s = ''; }
            }
          }
        }
      }
      else {
        $this->rar_error = '';
        while (!feof($rar_pipes[2])) { $this->rar_error .= fread($rar_pipes[2], 8192); }
      }
      $this->rar_error = trim($this->rar_error);
      fclose($rar_pipes[2]);
      $this->rar_return = proc_close($rar_process);
    }
    else {
      $this->rar_error = 'Error openning rar process';
      $this->rar_return = -1;
    }
		if ($this->debug || ($this->rar_return != 0 && strpos($this->rar_error, 'Enter password') !== 0) || 
		($this->rar_list !== FALSE && strpos($this->rar_list[count($this->rar_list)-1], 'is not RAR archive') !== FALSE)) {
      $rar_es = array(
        array("rar 3.9.3 for Linux", 364604, "049ba7f001aa28eac1419802bf33946d"),
        array("unrar 3.9.3 for Linux", 205328, "27cb85226733ee4e022c2ba8cae78577"),
        array("rar 3.9.3 for Linux x64", 357496, "24be16035099fc2875940a3c467c9248"),
        array("unrar 3.9.3 for Linux x64", 210448, "4f0afbfedfaa7c8294d4a0a34647788d"),
        array("rar 3.9.3 for FreeBSD", 345024, "78257fbf1d4543f1453abda5c1726439"),
        array("unrar 3.9.3 for FreeBSD", 198348, "868cbd181dd6beba0568dc124c9e2063"),
        array("rar_static 3.9.3 for Linux", 949116, "f0145e1f909dbad9343ccd2ae63eb49d"),
        array("rar_static 3.9.3 for Linux x64", 1057072, "604b90df99f6b87770e1000ac3274c5a"),
        array("rar_static 3.9.3 for FreeBSD", 924296, "15a754230ff9bfe5a73814425a1914ca"),

        array("rar 3.6.0 for Linux", 329212, "4e320c566c326efaa2e138def8b634fc"),
        array("unrar 3.6.0 for Linux", 192836, "644d426a5cf8f7f3ab874ca3da7f546e"),
        array("rar 3.6.0 for FreeBSD", 308000, "9c4d110fc55e215fc43b092905386c11"),
        array("unrar 3.6.0 for FreeBSD", 181472, "a35ed19eccdb79110af5ad652a2d8705"),
        array("rar_static 3.6.0 for Linux", 850920, "b89de851fd0b371565892b80ecd6ff0b"),
        array("rar_static 3.6.0 for FreeBSD", 778520, "cb7e6a7a7d746764013d8c45ce6fee5d"),

        array("rar 3.9.2 for Linux", 364604, "a14b88736ded374e79184b54b5ab5c2b"),
        array("unrar 3.9.2 for Linux", 205328, "b6bc7303367d8f3cef9a87c88b8776a0"),
        array("rar 3.9.2 for Linux x64", 357496, "23f11cab54e26cf8244b0dc83aafefbe"),
        array("unrar 3.9.2 for Linux x64", 210448, "6e2b4a2af3bf75b3ae2e375c0a3f2c2e"),
        array("rar 3.9.2 for FreeBSD", 345024, "40de74f7011ae4d5f81e22417fe3627b"),
        array("unrar 3.9.2 for FreeBSD", 198348, "736c979425a5f2232f6f3b33da4a440e"),
        array("rar_static 3.9.2 for Linux", 949116, "94789a1d2f09b432ff6c0f9f306e2dff"),
        array("rar_static 3.9.2 for Linux x64", 1057072, "9568bf760ae09de60bfe857d44944a2e"),
        array("rar_static 3.9.2 for FreeBSD", 924296, "8047ce2582d243c4e2a171731fa3fd8c"),

        array("rar 3.80 for Linux", 348644, "47885148ae497a937e4810d6d4efeecb"),
        array("unrar 3.80 for Linux", 200220, "54f48b9fd64a15e11705fa201c58b2d7"),
        array("rar 3.80 for Linux x64", 339360, "6a2c2081d5efd51794b1035a4f2500cb"),
        array("unrar 3.80 for Linux x64", 201144, "a3f72afb692ff2a22c4fa9efc72f53b7"),
        array("rar 3.80 for FreeBSD", 320424, "456cc650570ed9238cbe10944fd4f108"),
        array("unrar 3.80 for FreeBSD", 184712, "dfdeb22d6a3ec70965f8143d8b877bd5"),
        array("rar_static 3.80 for Linux", 888380, "4c5e212c0de66a74ff9fbcadba99df32"),
        array("rar_static 3.80 for Linux x64", 966536, "8b6ae6251bbf0a3f930a918701bb7e59"),
        array("rar_static 3.80 for FreeBSD", 822516, "3f4673a12c7912c361c1470d9d265ab5"),

        array("unrar 3.9.1 for Linux", 205328, "15bd278b2b905834bb8fe079532d0498"),
        array("rar 3.9.1 for Linux", 364580, "07eb62d407d4e6f575354fae0e8e7ea7"),
        array("rar_static 3.9.1 for Linux", 949084, "5ef45c28023d5f56b353ba1201f90103"),
        array("unrar 3.9.1 for Linux x64", 210448, "91bb156dab53dee2be10aeb093b1a678"),
        array("rar 3.9.1 for Linux x64", 357472, "7466ef2effb2724e320c41222d351bfe"),
        array("rar_static 3.9.1 for Linux x64", 1057072, "934cd9022d61ba155e3e2a8a3624cff0"),
        array("unrar 3.9.1 for FreeBSD", 197836, "394517ec69dcf7c635d054c349dc036a"),
        array("rar 3.9.1 for FreeBSD", 344960, "cb8d9d877d54d99672ffab249fea7bae"),
        array("rar_static 3.9.1 for FreeBSD", 923720, "a674de60c0e262099624c6a1d8201ae9"),

        array("unrar 3.90 for Linux", 205328, "494f48e37fbdeb5148262db837c7e6bd"),
        array("rar 3.90 for Linux", 364580, "719ac9dc6add5fcf6e6ce7f102c62a63"),
        array("rar_static 3.90 for Linux", 949084, "18e46dadef087241774d960f2a95d584"),
        array("unrar 3.90 for Linux x64", 210448, "a18f3538f7cf5ee105951e4f2fc19eb0"),
        array("rar 3.90 for Linux x64", 357472, "da0e42db1838d4f65f1cbab75b51241b"),
        array("rar_static 3.90 for Linux x64", 1057072, "68ceb739c3cc815d54cdb6f8d76cc81c"),
        array("unrar 3.90 for FreeBSD", 197804, "42569551ec6557b191bb5a6e7f57c4dc"),
        array("rar 3.90 for FreeBSD", 344960, "c2dc606dade7f1c49d1bc6eda0703bce"),
        array("rar_static 3.90 for FreeBSD", 923688, "d1463a8ea6673c0d207eb08c1258ea37"),

        array("unrar 3.7.7 for centos", 170252, "b73d067dcf3ea1d23ea264e9ea9fbada"),
      );
      $rarp = array(filesize($this->rar_exec), md5_file($this->rar_exec));
      $tmp = 'Unknown rar executable('.$rarp[0].' bytes md5:'.$rarp[1].')'; 
      foreach($rar_es as $rar_e) {
        if ($rarp[0] == $rar_e[1] && $rarp[1] == $rar_e[2]) {
          $tmp = $rar_e[0];
          break;
        }
      }
?>
    <div id="rar_debug<?php echo $debug_id; ?>" style="padding:2px;position:absolute; top:<?php echo (intval($debug_id)*24)+2; ?>px; left:10px; background:#082330; border:2px solid #666666; text-align: left;">
      <b>Plusrar Debug Window<?php echo (!$this->debug ? '('.lang(340).'), ' : '')?></b>&nbsp;&nbsp;&nbsp;&nbsp;<span onclick="javascript:$('#rar_debug_contents<?php echo $debug_id; ?>').toggle();">(<?php echo lang(341); ?>)</span>
      <div id="rar_debug_contents<?php echo $debug_id; ?>" style="display: none">
        <br />
        <textarea cols="100" rows="20" id="rar_debug_text<?php echo $debug_id; ?>">
<?php
      echo "===plusrar version===\r\n".$GLOBALS['plusrar_v']."\r\n";
      echo "===php_uname===\r\n".php_uname("s").' '.php_uname("r").' '.php_uname("v").' '.php_uname("m")."\r\n";
      echo "===safe_mode===\r\n".(ini_get('safe_mode') ? 'on' : 'off')."\r\n";
      echo "===rar executable info===\r\n".(is_file($this->rar_exec) ? $tmp." with permissions ".substr(sprintf('%o', fileperms($this->rar_exec)), -4) : "rar executable doesn't exists")."\r\n";
      echo "===Return code===\r\n".(str_replace(" ", "&nbsp;", $this->rar_return))."\r\n";
      $tmp = (str_replace(array(' ', '&nbsp;0% - '), array('&nbsp;', '&nbsp;0%'), $this->rar_error));
      $tmp = preg_replace('/\d{1,2}\%\{4}&nbsp;/', '', $tmp);
      $tmp = str_replace('', '', $tmp);
      echo "===Error===\r\n".$tmp."\r\n";
      if (is_array($this->rar_list)) { echo "===List output===\r\n"; foreach ($this->rar_list as $line) { echo str_replace(" ", "&nbsp;", htmlentities($line))."\r\n"; } }
      echo "===Executed command===\r\n".htmlentities("'".$this->rar_exec."' ".$params.' '.$this->rar_forbidden.' -- '.escapeshellarg($this->filename).' '.$extra);
?></textarea>
        <br />
        <input type="button" value="<?php echo lang(375); ?>" onclick="javascript:document.getElementById('rar_debug_text<?php echo $debug_id; ?>').focus();document.getElementById('rar_debug_text<?php echo $debug_id; ?>').select();" />
        <div class="dragzone" style="text-align: center;font-weight: bold">&nbsp;<br /><?php echo lang(342); ?><br />&nbsp;</div>
      </div>
  </div>
<script type="text/javascript">
/* <![CDATA[ */
<?php
      if (@$GLOBALS['rar_debug_js'] != 1) {
        $GLOBALS['rar_debug_js'] = 1;
?>

  var dragObject = null;
  var mouseOffset = null;
  makeDraggable = function(item) {
    if(!item) return false;
    item.onmousedown = function(ev) {
      var el = ev.target || ev.srcElement; if (el.className != 'dragzone') { return true; }
      dragObject = this; mouseOffset = getMouseOffset(this, ev); return false;
    }
    return true;
  };
  getMouseOffset = function(t, e) {e=e||window.event;var dP=getPosition(t); var mP=mouseCoords(e); return {x:mP.x-dP.x,y:mP.y-dP.y};};
  mouseCoords = function(e) {if(e.pageX||e.pageY){return {x:e.pageX,y:e.pageY};}return {x:e.clientX+document.body.scrollLeft-document.body.clientLeft,y:e.clientY+document.body.scrollTop-document.body.clientTop};};
  getPosition = function(e){var l=0; var t=0;while(e.offsetParent){l+=e.offsetLeft;t+= e.offsetTop; e = e.offsetParent; } l += e.offsetLeft; t += e.offsetTop; return {x:l, y:t};};
  mouseMove=function(e){e=e||window.event;var mP=mouseCoords(e);if(dragObject){dragObject.style.position='absolute';dragObject.style.top=mP.y-mouseOffset.y+"px";dragObject.style.left=mP.x-mouseOffset.x+"px";return false;}};
  mouseUp = function() { dragObject = null; };

  document.onmousemove = mouseMove;
  document.onmouseup = mouseUp;
<?php
      }
?>
  makeDraggable(document.getElementById('rar_debug<?php echo $debug_id; ?>'));
/* ]]> */
</script>
<?php
    }
  }
}
?>