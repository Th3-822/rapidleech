<?php
if (!defined('RAPIDLEECH')) {
    require('../deny.php');
    exit;
}

function getftpurl($host, $port, $url, $saveToFile = 0) {
    global $nn, $lastError, $PHP_SELF, $AUTH, $IS_FTP, $FtpBytesTotal, $FtpBytesReceived, $FtpTimeStart, $FtpChunkSize, $options;

    $ftp = new ftp(FALSE, FALSE);
    if (!$ftp->SetServer($host, (int) $port)) {
        $ftp->quit();
        $server = $host . ':' . $port;
        $lastError = sprintf(lang(79), $server) . "<br />" . '<a href="javascript:history.back(-1);">' . lang(78) . '</a><br /><br />';
        return FALSE;
    } else {
        if (!$ftp->connect()) {
            $ftp->quit();
            $lastError = sprintf(lang(79), $server) . "<br />" . '<a href="javascript:history.back(-1);">' . lang(78) . '</a><br /><br />';
            return FALSE;
        } else {
            if (!$ftp->login($AUTH["ftp"]["login"], $AUTH["ftp"]["password"])) {
                $ftp->quit();
                $lastError = lang(80) . "<br />" . '<a href="javascript:history.back(-1);">' . lang(78) . '</a><br /><br />';
                return FALSE;
            } else {
                echo('<p>');
                printf(lang(81), $host);
                echo('<br />');
                //$ftp->Passive(FALSE);
                $tmp = explode("/", $url);
                $ftp_file = array_pop($tmp);
                $ftp_dir = implode("/", $tmp);
                $ftp->chdir($ftp_dir);
                $fileSize = $FtpBytesTotal = $ftp->filesize($ftp_file);
                $FtpChunkSize = round($fileSize / 333);

                list($saveToFile, $tmp) = explode('?', $saveToFile);

                if ($options['file_size_limit'] > 0) {
                   if ($fileSize > $options['file_size_limit']*1024*1024) {
                      $lastError = lang(336) . bytesToKbOrMbOrGb ( $options['file_size_limit']*1024*1024 ) .".";
                      return false;
                   }
                }
                if (!empty($options['rename_prefix'])) {
                    $File_Name = $options['rename_prefix'] . '_' . basename($saveToFile);
                    $saveToFile = dirname($saveToFile) . PATH_SPLITTER . $File_Name;
                }
                if (!empty($options['rename_suffix'])) {
                    $ext = strrchr(basename($saveToFile), ".");
                    $before_ext = explode($ext, basename($saveToFile));
                    $File_Name = $before_ext[0] . '_' . $options['rename_suffix'] . $ext;
                    $saveToFile = dirname($saveToFile) . PATH_SPLITTER . $File_Name;
                }
                if ($options['rename_underscore']) {
                    $File_Name = str_replace(array(' ', '%20'), '_', basename($saveToFile));
                    $saveToFile = dirname($saveToFile) . PATH_SPLITTER . $File_Name;
                }

                $filetype = strrchr($saveToFile, ".");

                if (is_array($options['forbidden_filetypes']) && in_array(strtolower($filetype), $options['forbidden_filetypes'])) {
                    if ($options['forbidden_filetypes_block']) {
                        html_error(sprintf(lang(82), $filetype));
                    } else {
                        $saveToFile = str_replace($filetype, $options['rename_these_filetypes_to'], $saveToFile);
                    }
                }

                if (file_exists($saveToFile)) {
                    $saveToFile = dirname($saveToFile) . PATH_SPLITTER . time() . "_" . basename($saveToFile);
                }
                printf(lang(83), $saveToFile, bytesToKbOrMbOrGb($fileSize));
                echo "<br />";
?>
                <br />
                <table cellspacing="0" cellpadding="0" style="FONT-FAMILY: Tahoma; FONT-SIZE: 11px;">
                    <tr>
                        <td></td>
                        <td>
                            <div class="progressouter">
                                <div id="progress" class="ftpprogress">
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
                <br />
                <div id="resume" align="center" style="FONT-FAMILY: Tahoma; FONT-SIZE: 11px;"></div>
                <script type="text/javascript">
                    /* <![CDATA[ */
                    function pr(percent, received, speed){
                        document.getElementById("received").innerHTML = '<b>' + received + '</b>';
                        document.getElementById("percent").innerHTML = '<b>' + percent + '%</b>';
                        document.getElementById("progress").style.width = percent + '%';
                        document.getElementById("speed").innerHTML = '<b>' + speed + ' KB/s</b>';
                        document.title = 'Downloaded ' + percent + '%';
                        return true;
                    }

                    function mail(str, field) {
                        document.getElementById("mailPart." + field).innerHTML = str;
                        return true;
                    }
                    /* ]]> */
                </script>
                <br />
<?php
                $FtpTimeStart = getmicrotime();
                if ($ftp->get($ftp_file, $saveToFile)) {
                    return array("time" => sec2time(round($time)),
                        "speed" => @round($FtpBytesTotal / 1024 / (getmicrotime() - $FtpTimeStart), 2),
                        "received" => TRUE,
                        "size" => bytesToKbOrMbOrGb($fileSize),
                        "bytesReceived" => $FtpBytesReceived,
                        "bytesTotal" => $FtpBytesTotal,
                        "file" => $saveToFile);
                } else {
                    return FALSE;
                }
                $ftp->quit();
            }
        }
    }
}

function updateFtpProgress($bytesReceived) {
    global $FtpBytesTotal, $FtpBytesReceived, $FtpTimeStart, $FtpLastChunkTime, $FtpChunkSize, $FtpLast, $FtpUpload, $FtpUploadBytesSent;
    if ($FtpUpload) {
        $FtpUploadBytesSent += $bytesReceived;
        $bytesReceived = $FtpUploadBytesSent;
    }
    $FtpBytesReceived = $bytesReceived;
    if ($bytesReceived > $FtpLast + $FtpChunkSize) {
        $time = getmicrotime() - $FtpTimeStart;
        $chunkTime = $time - $FtpLastChunkTime;
        $FtpLastChunkTime = $time;
        $speed = round($FtpChunkSize / 1024 / $chunkTime, 2);
        # $FtpBytesReceived = $bytesReceived;
        if ($bytesReceived > $FtpBytesTotal) {
            $percent = 100;
        } else {
            $percent = round($bytesReceived / $FtpBytesTotal * 100, 2);
        }
        $FtpLast = $bytesReceived;
        echo '<script type="text/javascript">pr(' . $percent . ", '" . bytesToKbOrMbOrGb($bytesReceived) . "', " . $speed . ")</script>\r\n";
    }
}

class ftp_base {
    /* Public variables */

    var $LocalEcho = FALSE;
    var $Verbose = FALSE;
    var $OS_local;

    /* Private variables */
    var $_lastaction = NULL;
    var $_errors;
    var $_type;
    var $_umask;
    var $_timeout;
    var $_passive;
    var $_host;
    var $_fullhost;
    var $_port;
    var $_datahost;
    var $_dataport;
    var $_ftp_control_sock;
    var $_ftp_data_sock;
    var $_ftp_temp_sock;
    var $_login;
    var $_password;
    var $_connected;
    var $_ready;
    var $_code;
    var $_message;
    var $_can_restore;
    var $_port_available;
    var $_error_array = array();
    var $AuthorizedTransferMode = array(
        FTP_AUTOASCII,
        FTP_ASCII,
        FTP_BINARY
    );
    var $OS_FullName = array(
        FTP_OS_Unix => 'UNIX',
        FTP_OS_Windows => 'WINDOWS',
        FTP_OS_Mac => 'MACOS'
    );
    var $NewLineCode = array(
        FTP_OS_Unix => "\n",
        FTP_OS_Mac => "\r",
        FTP_OS_Windows => "\r\n"
    );
    var $AutoAsciiExt = array("ASP", "BAT", "C", "CPP", "CSV", "H", "HTM", "HTML", "SHTML", "INI", "LOG", "PHP", "PHP3", "PL", "PERL", "SH", "SQL", "TXT");

    /* Constructor */

    function ftp_base($port_mode=FALSE) {
        $this->_port_available = ($port_mode == TRUE);
        $this->SendMSG("Staring FTP client class with" . ($this->_port_available ? "" : "out") . " PORT mode support");
        $this->_connected = FALSE;
        $this->_ready = FALSE;
        $this->_can_restore = FALSE;
        $this->_code = 0;
        $this->_message = "";
        $this->SetUmask(0022);
        $this->SetType(FTP_AUTOASCII);
        $this->SetTimeout(30);
        $this->Passive(!$this->_port_available);
        $this->_login = "anonymous";
        $this->_password = "anon@ftp.com";
        $this->OS_local = FTP_OS_Unix;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $this->OS_local = FTP_OS_Windows;
        elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'MAC') $this->OS_local = FTP_OS_Mac;
    }

// <!-- --------------------------------------------------------------------------------------- -->
// <!--       Public functions                                                                  -->
// <!-- --------------------------------------------------------------------------------------- -->
    function parselisting($list) {
//Parses i line like:"drwxrwx---  2 owner group 4096 Apr 23 14:57 text"
        if (preg_match("/^([-ld])([rwxst-]+)\s+(\d+)\s+([-_\w]+)\s+([-_\w]+)\s+(\d+)\s+(\w{3})\s+(\d+)\s+([\:\d]+)\s+(.+)$/i", $list, $ret)) {
            $v = array(
                "type" => ($ret[1] == "-" ? "f" : $ret[1]),
                "perms" => 0,
                "inode" => $ret[3],
                "owner" => $ret[4],
                "group" => $ret[5],
                "size" => $ret[6],
                "date" => $ret[7] . " " . $ret[8] . " " . $ret[9],
                "name" => $ret[10]
            );
            $v["perms"]+=00400 * (int) ($ret[2]{0} == "rb");
            $v["perms"]+=00200 * (int) ($ret[2]{1} == "wb");
            $v["perms"]+=00100 * (int) in_array($ret[2]{2}, array("x", "s"));
            $v["perms"]+=00040 * (int) ($ret[2]{3} == "rb");
            $v["perms"]+=00020 * (int) ($ret[2]{4} == "wb");
            $v["perms"]+=00010 * (int) in_array($ret[2]{5}, array("x", "s"));
            $v["perms"]+=00004 * (int) ($ret[2]{6} == "rb");
            $v["perms"]+=00002 * (int) ($ret[2]{7} == "wb");
            $v["perms"]+=00001 * (int) in_array($ret[2]{8}, array("x", "t"));
            $v["perms"]+=04000 * (int) in_array($ret[2]{2}, array("S", "s"));
            $v["perms"]+=02000 * (int) in_array($ret[2]{5}, array("S", "s"));
            $v["perms"]+=01000 * (int) in_array($ret[2]{8}, array("T", "t"));
        }
        return $v;
    }

    function SendMSG($message = "", $crlf=true) {
        if ($this->Verbose) {
            echo $message . ($crlf ? CRLF : "");
            flush();
        }
        return TRUE;
    }

    function SetType($mode=FTP_AUTOASCII) {
        if (!in_array($mode, $this->AuthorizedTransferMode)) {
            $this->SendMSG("Wrong type");
            return FALSE;
        }
        $this->_type = $mode;
        $this->_data_prepare($mode);
        $this->SendMSG("Transfer type: " . ($this->_type == FTP_BINARY ? "binary" : ($this->_type == FTP_ASCII ? "ASCII" : "auto ASCII") ));
        return TRUE;
    }

    function Passive($pasv=NULL) {
        if (is_null($pasv)) $this->_passive = !$this->_passive;
        else $this->_passive = $pasv;
        if (!$this->_port_available and !$this->_passive) {
            $this->SendMSG("Only passive connections available!");
            $this->_passive = TRUE;
            return FALSE;
        }
        $this->SendMSG("Passive mode " . ($this->_passive ? "on" : "off"));
        return TRUE;
    }

    function SetServer($host, $port=21, $reconnect=true) {
        if (!is_long($port)) {
            $this->verbose = true;
            $this->SendMSG("Incorrect port syntax");
            return FALSE;
        } else {
            $ip = @gethostbyname($host);
            $dns = @gethostbyaddr($host);
            if (!$ip) $ip = $host;
            if (!$dns) $dns = $host;
            if (ip2long($ip) === -1) {
                $this->SendMSG('Wrong host name/address "' . $host . '"');
                return FALSE;
            }
            $this->_host = $ip;
            $this->_fullhost = $dns;
            $this->_port = $port;
            $this->_dataport = $port - 1;
        }
        $this->SendMSG('Host "' . $this->_fullhost . "(" . $this->_host . "):" . $this->_port . '"');
        if ($reconnect) {
            if ($this->_connected) {
                $this->SendMSG("Reconnecting");
                if (!$this->quit(FTP_FORCE)) return FALSE;
                if (!$this->connect()) return FALSE;
            }
        }
        return TRUE;
    }

    function SetUmask($umask=0022) {
        $this->_umask = $umask;
        umask($this->_umask);
        $this->SendMSG("UMASK 0" . decoct($this->_umask));
        return TRUE;
    }

    function SetTimeout($timeout=30) {
        $this->_timeout = $timeout;
        $this->SendMSG("Timeout " . $this->_timeout);
        if ($this->_connected) if (!$this->_settimeout($this->_ftp_control_sock)) return FALSE;
        return TRUE;
    }

    function connect() {
        $this->SendMsg('Local OS : ' . $this->OS_FullName[$this->OS_local]);
        if (!($this->_ftp_control_sock = $this->_connect($this->_host, $this->_port))) {
            $this->SendMSG('Error : Cannot connect to remote host "' . $this->_fullhost . " :" . $this->_port . '"');
            return FALSE;
        }
        $this->SendMSG('Connected to remote host "' . $this->_fullhost . ":" . $this->_port . '". Waiting for greeting.');
        do {
            if (!$this->_readmsg()) return FALSE;
            if (!$this->_checkCode()) return FALSE;
            $this->_lastaction = time();
        } while ($this->_code < 200);
        $this->_ready = true;
        return TRUE;
    }

    function quit($force=false) {
        if ($this->_ready) {
            if (!$this->_exec("QUIT") and !$force) return FALSE;
            if (!$this->_checkCode() and !$force) return FALSE;
            $this->_ready = false;
            $this->SendMSG("Session finished");
        }
        $this->_quit();
        return TRUE;
    }

    function login($user=NULL, $pass=NULL) {
        if (!is_null($user)) $this->_login = $user;
        else $this->_login = "anonymous";
        if (!is_null($pass)) $this->_password = $pass;
        else $this->_password = "anon@anon.com";
        if (!$this->_exec("USER " . $this->_login, "login")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        if ($this->_code != 230) {
            if (!$this->_exec((($this->_code == 331) ? "PASS " : "ACCT ") . $this->_password, "login")) return FALSE;
            if (!$this->_checkCode()) return FALSE;
        }
        $this->SendMSG("Authentication succeeded");
        $this->_can_restore = $this->restore(100);
        $this->SendMSG("This server can" . ($this->_can_restore ? "" : "'t") . " resume broken uploads/downloads");
        return TRUE;
    }

    function pwd() {
        if (!$this->_exec("PWD", "pwd")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return preg_replace("#^[0-9]{3} \"(.+)\" .+" . CRLF."#", "\\1", $this->_message);
    }

    function cdup() {
        if (!$this->_exec("CDUP", "cdup")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return true;
    }

    function chdir($pathname) {
        if (!$this->_exec("CWD " . $pathname, "chdir")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return TRUE;
    }

    function rmdir($pathname) {
        if (!$this->_exec("RMD " . $pathname, "rmdir")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return TRUE;
    }

    function mkdir($pathname) {
        if (!$this->_exec("MKD " . $pathname, "mkdir")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return TRUE;
    }

    function rename($from, $to) {
        if (!$this->_exec("RNFR " . $from, "rename")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        if ($this->_code == 350) {
            if (!$this->_exec("RNTO " . $to, "rename")) return FALSE;
            if (!$this->_checkCode()) return FALSE;
        } else return FALSE;
        return TRUE;
    }

    function filesize($pathname) {
        if (!$this->_exec("SIZE " . $pathname, "filesize")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return preg_replace("#^[0-9]{3} ([0-9]+)" . CRLF."#", "\\1", $this->_message);
    }

    function mdtm($pathname) {
        if (!$this->_exec("MDTM " . $pathname, "mdtm")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        $mdtm = preg_replace("#^[0-9]{3} ([0-9]+)" . CRLF."#", "\\1", $this->_message);
        $date = sscanf($mdtm, "%4d%2d%2d%2d%2d%2d");
        $timestamp = mktime($date[3], $date[4], $date[5], $date[1], $date[2], $date[0]);
        return $timestamp;
    }

    function systype() {
        if (!$this->_exec("SYST", "systype")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        $DATA = explode(" ", $this->_message);
        return $DATA[1];
    }

    function delete($pathname) {
        if (!$this->_exec("DELE " . $pathname, "delete")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return TRUE;
    }

    function site($command, $fnction="site") {
        if (!$this->_exec("SITE " . $command, $fnction)) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return TRUE;
    }

    function chmod($pathname, $mode) {
        if (!$this->site("CHMOD " . decoct($mode) . " " . $pathname, "chmod")) return FALSE;
        return TRUE;
    }

    function restore($from) {
        if (!$this->_exec("REST " . $from, "restore")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return TRUE;
    }

    function features() {
        if (!$this->_exec("FEAT", "features")) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return preg_split("/[" . CRLF . "]+/", preg_replace("#[0-9]{3}[ -][^" . CRLF . "]*" . CRLF."#", "", $this->_message), -1, PREG_SPLIT_NO_EMPTY);
    }

    function rawlist($arg="", $pathname="") {
        return $this->_list(($arg ? " " . $arg : "") . ($pathname ? " " . $pathname : ""), "LIST", "rawlist");
    }

    function nlist($arg="", $pathname="") {
        return $this->_list(($arg ? " " . $arg : "") . ($pathname ? " " . $pathname : ""), "NLST", "nlist");
    }

    function is_exists($pathname) {
        if (!($remote_list = $this->nlist("-a", dirname($pathname)))) {
            $this->SendMSG("Error : Cannot get remote file list");
            return -1;
        }
        reset($remote_list);
        while (list(, $value) = each($remote_list)) {
            if ($value == basename($pathname)) {
                $this->SendMSG("Remote file " . $pathname . " exists");
                return TRUE;
            }
        }
        $this->SendMSG("Remote file " . $pathname . " does not exist");
        return FALSE;
    }

    function get($remotefile, $localfile=NULL) {
        if (is_null($localfile)) $localfile = $remotefile;
        if (@file_exists($localfile)) $this->SendMSG("Warning: local file will be overwritten");
        $fp = @fopen($localfile, "wb");
        if (!$fp) {
            $this->PushError("get", "can't open local file", 'Cannot create "' . $localfile . '"');
            return FALSE;
        }
        $pi = pathinfo($remotefile);
        if ($this->_type == FTP_ASCII or ($this->_type == FTP_AUTOASCII and in_array(strtoupper($pi["extension"]), $this->AutoAsciiExt))) $mode = FTP_ASCII;
        else $mode=FTP_BINARY;
        if (!$this->_data_prepare($mode)) {
            fclose($fp);
            return FALSE;
        }
        if ($this->_can_restore) $this->restore(0);
        if (!$this->_exec("RETR " . $remotefile, "get")) {
            $this->_data_close();
            fclose($fp);
            return FALSE;
        }
        if (!$this->_checkCode()) {
            $this->_data_close();
            fclose($fp);
            return FALSE;
        }
        $out = $this->_data_read($mode, $fp);
        fclose($fp);
        $this->_data_close();
        if (!$this->_readmsg()) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return $out;
    }

    function get2($remotefile, $from) {
        $mode = FTP_BINARY;
        if (!$this->_data_prepare($mode)) {
            return FALSE;
        }
        if ($this->_can_restore)
            $this->restore($from);
        if (!$this->_exec("RETR " . $remotefile, "get")) {
            $this->_data_close();
            return FALSE;
        }
        if (!$this->_checkCode()) {
            $this->_data_close();
            return FALSE;
        }
        $out = $this->_data_read2();
        $this->_data_close();
        if (!$this->_readmsg()) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return $out;
    }

    function put($localfile, $remotefile=NULL) {
        if (is_null($remotefile)) $remotefile = $localfile;
        if (!@file_exists($localfile)) {
            $this->PushError("put", "can't open local file", 'No such file or directory "' . $localfile . '"');
            return FALSE;
        }
        $fp = @fopen($localfile, "rb");
        if (!$fp) {
            $this->PushError("put", "can't open local file", 'Cannot read file "' . $localfile . '"');
            return FALSE;
        }
        $pi = pathinfo($localfile);
        if ($this->_type == FTP_ASCII or ($this->_type == FTP_AUTOASCII and in_array(strtoupper($pi["extension"]), $this->AutoAsciiExt))) $mode = FTP_ASCII;
        else $mode=FTP_BINARY;
        if (!$this->_data_prepare($mode)) {
            fclose($fp);
            return FALSE;
        }
        if ($this->_can_restore) $this->restore(0);
        if (!$this->_exec("STOR " . $remotefile, "put")) {
            $this->_data_close();
            fclose($fp);
            return FALSE;
        }
        if (!$this->_checkCode()) {
            $this->_data_close();
            fclose($fp);
            return FALSE;
        }
        $ret = $this->_data_write($mode, $fp);
        fclose($fp);
        $this->_data_close();
        if (!$this->_readmsg()) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        return $ret;
    }

// <!-- --------------------------------------------------------------------------------------- -->
// <!--       Private functions                                                                 -->
// <!-- --------------------------------------------------------------------------------------- -->
    function _checkCode() {
        return ($this->_code < 400 and $this->_code > 0);
    }

    function _list($arg="", $cmd="LIST", $fnction="_list") {
        if (!$this->_data_prepare()) return FALSE;
        if (!$this->_exec($cmd . $arg, $fnction)) {
            $this->_data_close();
            return FALSE;
        }
        if (!$this->_checkCode()) {
            $this->_data_close();
            return FALSE;
        }
        $out = $this->_data_read();
        $this->_data_close();
        if (!$this->_readmsg()) return FALSE;
        if (!$this->_checkCode()) return FALSE;
        if ($out === FALSE) return FALSE;
        $out = preg_split("/[" . CRLF . "]+/", $out, -1, PREG_SPLIT_NO_EMPTY);
        $this->SendMSG(implode($this->NewLineCode[$this->OS_local], $out));
        return $out;
    }

// <!-- --------------------------------------------------------------------------------------- -->
// <!-- Partie : gestion des erreurs                                                            -->
// <!-- --------------------------------------------------------------------------------------- -->
// Genere une erreur pour traitement externe a la classe
    function PushError($fctname, $msg, $desc=false) {
        $error = array();
        $error['time'] = time();
        $error['fctname'] = $fctname;
        $error['msg'] = $msg;
        $error['desc'] = $desc;
        if ($desc) $tmp = ' (' . $desc . ')';
        else $tmp='';
        $this->SendMSG($fctname . ': ' . $msg . $tmp);
        return(array_push($this->_error_array, $error));
    }

// Recupere une erreur externe
    function PopError() {
        if (count($this->_error_array)) return(array_pop($this->_error_array));
        else return(false);
    }

}

$mod_sockets = TRUE;
if (!extension_loaded('sockets')) {
    $prefix = (PHP_SHLIB_SUFFIX == 'dll') ? 'php_' : '';
    if (!@dl($prefix . 'sockets.' . PHP_SHLIB_SUFFIX)) $mod_sockets = FALSE;
}

$mod_sockets = TRUE;
if (!extension_loaded('sockets')) {
    $prefix = (PHP_SHLIB_SUFFIX == 'dll') ? 'php_' : '';
    if (!@dl($prefix . 'sockets.' . PHP_SHLIB_SUFFIX)) $mod_sockets = FALSE;
}

class ftp extends ftp_base {

    function ftp($verb=FALSE, $le=FALSE) {
        $this->LocalEcho = $le;
        $this->Verbose = $verb;
        $this->ftp_base();
    }

// <!-- --------------------------------------------------------------------------------------- -->
// <!--       Private functions                                                                 -->
// <!-- --------------------------------------------------------------------------------------- -->

    function _settimeout($sock) {
        echo"===";
        /* if(!@stream_set_timeout($sock, $this->_timeout)) {
          $this->PushError('_settimeout','socket set send timeout');
          $this->_quit();
          return FALSE;
          }
         */return TRUE;
    }

    function _connect($host, $port) {
        $this->SendMSG("Creating socket");
        $sock = @fsockopen($host, $port, $errno, $errstr, $this->_timeout);
        if (!$sock) {
            $this->PushError('_connect', 'socket connect failed', $errstr . " (" . $errno . ")");
            return FALSE;
        }
        $this->_connected = true;
        return $sock;
    }

    function _readmsg($fnction="_readmsg") {
        if (!$this->_connected) {
            $this->PushError($fnction, 'Connect first');
            return FALSE;
        }
        $result = true;
        $this->_message = "";
        $this->_code = 0;
        $go = true;
        do {
            $tmp = @fgets($this->_ftp_control_sock, 512);
            if ($tmp === false) {
                $go = $result = false;
                $this->PushError($fnction, 'Read failed');
            } else {
                $this->_message.=$tmp;
//for($i=0; $i<strlen($this->_message); $i++)
//if(ord($this->_message[$i])<32) echo "#".ord($this->_message[$i]); else echo $this->_message[$i];
//echo CRLF;
                if (preg_match("/^([0-9]{3})(-(.*" . CRLF . ")+\\1)? [^" . CRLF . "]+" . CRLF . "$/", $this->_message, $regs)) $go = false;
            }
        } while ($go);
        if ($this->LocalEcho) echo "GET < " . rtrim($this->_message, CRLF) . CRLF;
        $this->_code = (int) $regs[1];
        return $result;
    }

    function _exec($cmd, $fnction="_exec") {
        if (!$this->_ready) {
            $this->PushError($fnction, 'Connect first');
            return FALSE;
        }
        if ($this->LocalEcho) echo "PUT > ", $cmd, CRLF;
        $status = @fputs($this->_ftp_control_sock, $cmd . CRLF);
        if ($status === false) {
            $this->PushError($fnction, 'socket write failed');
            return FALSE;
        }
        $this->_lastaction = time();
        if (!$this->_readmsg($fnction)) return FALSE;
        return TRUE;
    }

    function _data_prepare($mode=FTP_ASCII) {
        if ($mode == FTP_BINARY) {
            if (!$this->_exec("TYPE I", "_data_prepare")) return FALSE;
        } else {
            if (!$this->_exec("TYPE A", "_data_prepare")) return FALSE;
        }
        if ($this->_passive) {
            if (!$this->_exec("PASV", "pasv")) {
                $this->_data_close();
                return FALSE;
            }
            if (!$this->_checkCode()) {
                $this->_data_close();
                return FALSE;
            }
            $ip_port = explode(",", preg_replace("#^.+ \\(?([0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]+,[0-9]+)\\)?.*" . CRLF . "$"."#", "\\1", $this->_message));
            $this->_datahost = $ip_port[0] . "." . $ip_port[1] . "." . $ip_port[2] . "." . $ip_port[3];
            $this->_dataport = (((int) $ip_port[4]) << 8) + ((int) $ip_port[5]);
            $this->SendMSG("Connecting to " . $this->_datahost . ":" . $this->_dataport);
            $this->_ftp_data_sock = @fsockopen($this->_datahost, $this->_dataport, $errno, $errstr, $this->_timeout);
            if (!$this->_ftp_data_sock) {
                $this->PushError("_data_prepare", "fsockopen fails", $errstr . " (" . $errno . ")");
                $this->_data_close();
                return FALSE;
            } else $this->_ftp_data_sock;
        } else {
            $this->SendMSG("Only passive connections available!");
            return FALSE;
        }
        return TRUE;
    }

    function _data_read($mode=FTP_ASCII, $fp=NULL) {
        $NewLine = $this->NewLineCode[$this->OS_local];
        if (is_resource($fp)) $out = 0;
        else $out="";
        if (!$this->_passive) {
            $this->SendMSG("Only passive connections available!");
            return FALSE;
        }
        if ($mode != FTP_BINARY) {
            while (!feof($this->_ftp_data_sock)) {
                $tmp = fread($this->_ftp_data_sock, 4096);
                $line.=$tmp;
                if (!preg_match("/" . CRLF . "$/", $line)) continue;
                $line = rtrim($line, CRLF) . $NewLine;
                if (is_resource($fp)) {
                    $out+=fwrite($fp, $line, strlen($line));
                    updateFtpProgress($out);
                } else {
                    $out.=$line;
                }
                $line = "";
            }
        } else {
            while (!feof($this->_ftp_data_sock)) {
                $block = fread($this->_ftp_data_sock, 4096);
                if (is_resource($fp)) {
                    $out+=fwrite($fp, $block, strlen($block));
                    updateFtpProgress($out);
                } else {
                    $out.=$line;
                }
            }
        }
        return $out;
    }

    function _data_read2() {
        $NewLine = $this->NewLineCode[$this->OS_local];
        $out = 0;
        if (!$this->_passive) {
            $this->SendMSG("Only passive connections available!");
            return FALSE;
        }
        while (!feof($this->_ftp_data_sock)) {
            $block = fread($this->_ftp_data_sock, 4096);
            $out+=strlen($block);
            echo $block;
        }
        return $out;
    }

    function _data_write($mode=FTP_ASCII, $fp=NULL) {
        $NewLine = $this->NewLineCode[$this->OS_local];
        if (is_resource($fp)) $out = 0;
        else $out="";
        if (!$this->_passive) {
            $this->SendMSG("Only passive connections available!");
            return FALSE;
        }
        if (is_resource($fp)) {
            while (!feof($fp)) {
                $line = fgets($fp, 4096);
                if ($mode != FTP_BINARY) $line = rtrim($line, CRLF) . CRLF;
                do {
                    if (($res = @fwrite($this->_ftp_data_sock, $line)) === FALSE) {
                        $this->PushError("_data_write", "Can't write to socket");
                        return FALSE;
                    } else {
                        updateFtpProgress(strlen($line));
                    }
                    $line = substr($line, $res);
                } while ($line != "");
            }
        } else {
            if ($mode != FTP_BINARY) $fp = rtrim($fp, $NewLine) . CRLF;
            do {
                if (($res = @fwrite($this->_ftp_data_sock, $fp)) === FALSE) {
                    $this->PushError("_data_write", "Can't write to socket");
                    return FALSE;
                } else {
                    updateFtpProgress(strlen($fp));
                }
                $fp = substr($fp, $res);
            } while ($fp != "");
        }
        return TRUE;
    }

    function _data_close() {
        @fclose($this->_ftp_data_sock);
        $this->SendMSG("Disconnected data from remote host");
        return TRUE;
    }

    function _quit($force=FALSE) {
        if ($this->_connected or $force) {
            @fclose($this->_ftp_control_sock);
            $this->_connected = false;
            $this->SendMSG("Socket closed");
        }
    }

}
?>