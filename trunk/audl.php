<?php
define('RAPIDLEECH', 'yes');
error_reporting(0);
//ini_set('display_errors', 1);
set_time_limit(0);
ini_alter("memory_limit", "1024M");
ob_end_clean();
ob_implicit_flush(TRUE);
ignore_user_abort(1);
clearstatcache();
$PHP_SELF = !$PHP_SELF ? $_SERVER["PHP_SELF"] : $PHP_SELF;
define('HOST_DIR', 'hosts/');
define('IMAGE_DIR', 'images/');
define('CLASS_DIR', 'classes/');
define('CONFIG_DIR', 'configs/');
define('RAPIDLEECH', 'yes');
define('ROOT_DIR', realpath("./"));
define('PATH_SPLITTER', (strstr(ROOT_DIR, "\\") ? "\\" : "/"));
require_once("configs/config.php");
if (substr($download_dir,-1) != '/') $download_dir .= '/';
define('DOWNLOAD_DIR', (substr($download_dir, 0, 6) == "ftp://" ? '' : $download_dir));
$nn = "\r\n";
require_once("classes/other.php");

if ($login === true && (!isset($_SERVER['PHP_AUTH_USER']) || ($loggeduser = logged_user($users)) === false))
	{
		header("WWW-Authenticate: Basic realm=\"RAPIDLEECH PLUGMOD\"");
		header("HTTP/1.0 401 Unauthorized");
		exit("<html>$nn<head>$nn<title>RAPIDLEECH PLUGMOD</title>$nn<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\">$nn</head>$nn<body>$nn<h1>$nn<center>$nn<a href=http://www.rapidleech.com>RapidLeech</a>: Access Denied - Wrong Username or Password$nn</center>$nn</h1>$nn</body>$nn</html>");
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RAPIDLEECH PLUGMOD - Auto Transload</title>
<style type="text/css">
<!--
@import url("images/rl_style_pm.css");
-->
.container td {
	background-color:#001825;
	padding:2px;
}
</style>
</head>
<body>
<center><img src="images/logo_pm.gif" alt="RAPIDLEECH PLUGMOD"></center><br><br>
<center>
<?php
if ($_REQUEST["GO"] == "GO") {
	$getlinks=explode("\r\n",trim($_REQUEST['links']));
	
	if (!count($getlinks) || (trim($_REQUEST['links']) == "")) {
		html_error("No link submited");
	}
	
	if ($_REQUEST['server_side'] == 'on') {
		// Get supported download plugins
		require_once(HOST_DIR."download/hosts.php");
		require_once(CLASS_DIR."ftp.php");
		require_once(CLASS_DIR."http.php");
		if (isset($_POST["useproxy"]) && $_POST["useproxy"] == true && (!$_POST["proxy"] || !strstr($_POST["proxy"], ":"))) {
			html_error("Bad proxy server address", 0);
		}
?>
<table width="90%" style="border:1px solid #666" class="container" cellspacing="1">
<tr>
<td width="80%" align="center"><b>Link</b></td>
<td width="70" align="center"><b>Status</b></td>
</tr>
<?php 
		for ($i = 0; $i < count($getlinks); $i++) {
			echo "<tr><td width=\"80%\" nowrap>".$getlinks[$i]."</td><td width=\"70\" id=\"status".$i."\">Waiting</td></tr>".$nn;
		}
?>
</table>
<script type="text/javascript" language="javascript"><!--
function updateStatus(id, status)
{
	document.getElementById("status"+id).innerHTML = status;
}
function resetProgress()
{
	document.getElementById("received").innerHTML = '0 KB';
	document.getElementById("percent").innerHTML = '0%';
	document.getElementById("progress").style.width = '0%';
	document.getElementById("speed").innerHTML = '0 KB/s';
	document.title = 'RAPIDLEECH PLUGMOD - Auto Download';
}
//--></script>
<?php 
		for ($i = 0; $i < count($getlinks); $i++) {
			$isHost = false;
			$hideDiv = false;
			unset($FileName);
			unset($force_name);
			//$bytesReceived = 0; // fix for GLOBAL in geturl()
			unset($bytesReceived);
			$LINK = $getlinks[$i];
			$Referer = $LINK;
			$Url = parse_url($LINK);
			$_GET = Array();
			$_GET["GO"] = "GO"; // for insert_location()
			$_GET["path"] = ((substr($download_dir, 0, 6) != "ftp://") ? realpath(DOWNLOAD_DIR) : $download_dir);
	
			if (isset($_POST["useproxy"]) && $_POST["useproxy"] == true) {
				$_GET["useproxy"] = "on";
				$_GET["proxy"] = $_POST["proxy"];
				$pauth = ($_POST["proxyuser"] && $_POST["proxypass"]) ? base64_encode($_POST["proxyuser"].":".$_POST["proxypass"]) : "";
			}
			
			if (isset($_POST['premium_acc'])) {
				$_GET["premium_acc"] = "on";
				$_GET["premium_user"] = $_POST["premium_user"];
				$_GET["premium_pass"] = $_POST["premium_pass"];
			}
	
			if ($Url['scheme'] != 'http' && $Url['scheme'] != 'https' && $Url['scheme'] != 'ftp') {
				echo "<script type=\"text/javascript\" language=\"javascript\">updateStatus(".$i.", 'Invalid URL');</script>".$nn;
			} else {
				echo "<div id=\"progress".$i."\" style=\"display:block;\">".$nn;
				foreach ($host as $site => $file) {
					if (preg_match("/^(.+\.)?".$site."$/i", $Url["host"])) {
						require(HOST_DIR.'download/'.$file);
						$isHost = true;
					}
				}
				if (!$isHost) {
					$FileName = basename($Url["path"]);
					insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&port=".$Url["port"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=&partSize=&method=&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK));
				}
				echo "<script type=\"text/javascript\" language=\"javascript\">updateStatus(".$i.", 'Preparing');</script>".$nn;
				$redir = "";
				$lastError = "";
				do {
					list($_GET["filename"],$tmp) = explode('?',urldecode(trim($_GET["filename"])));
					$_GET["saveto"] = urldecode(trim($_GET["saveto"]));
					$_GET["host"] = urldecode(trim($_GET["host"]));
					$_GET["path"] = urldecode(trim($_GET["path"]));
					$_GET["port"] = $_GET["port"] ? urldecode(trim($_GET["port"])) : 80;
					$_GET["referer"] = $_GET["referer"] ? urldecode(trim($_GET["referer"])) : 0;
					$_GET["link"] = urldecode(trim($_GET["link"]));
					$_GET["post"] = $_GET["post"] ? unserialize(stripslashes(urldecode(trim($_GET["post"])))) : 0;
					$_GET["cookie"] = $_GET["cookie"] ? urldecode(trim($_GET["cookie"])) : 0;
	
					$redirectto = "";
	
					$pauth = urldecode(trim($_GET["pauth"]));
					$auth = urldecode(trim($_GET["auth"]));
	
					if($_GET["auth"]) {
						$AUTH["use"] = TRUE;
						$AUTH["str"] = $_GET["auth"];
					} else {
						unset($AUTH);
					}
	
					$ftp = parse_url($_GET["link"]);
					$IS_FTP = $ftp["scheme"] == "ftp" ? TRUE : FALSE;
					$AUTH["ftp"] = array("login" => ($ftp["user"] ? $ftp["user"] : "anonymous"), "password" => ($ftp["pass"] ? $ftp["pass"] : "anonymous@leechget.com"));
	
					$pathWithName = $_GET["saveto"].PATH_SPLITTER.$_GET["filename"];
					while (stristr($pathWithName, "\\\\")) {
						$pathWithName = str_replace("\\\\", "\\", $pathWithName);				
					}
					list($pathWithName,$tmp) = explode('?',$pathWithName);
	
					echo "<script type=\"text/javascript\" language=\"javascript\">updateStatus(".$i.", 'Started');</script>".$nn;
					if ($ftp["scheme"] == "ftp" && !$_GET["proxy"]) {
						$file = getftpurl($_GET["host"], $ftp["port"] ? $ftp["port"] : 21, $_GET["path"], $pathWithName);
					} else {
						$_GET["force_name"] ? $force_name = urldecode($_GET["force_name"]) : '';
						$file = geturl($_GET["host"], $_GET["port"], $_GET["path"], $_GET["referer"], $_GET["cookie"], $_GET["post"], $pathWithName, $_GET["proxy"], $pauth, $auth, $ftp["scheme"]);
					}
					if ($redir && $lastError && stristr($lastError,"Error! it is redirected to [")) {
						$redirectto = trim(cut_str($lastError,"Error! it is redirected to [","]"));
						$_GET["link"] = $redirectto;
						$purl = parse_url($redirectto);
						list($_GET["filename"],$tmp) = explode('?',basename($redirectto));
						$_GET["host"] = $purl["host"];
						$_GET["path"] = $purl["path"].($purl["query"] ? "?".$purl["query"] : "");
						$lastError = "";
					}
					if ($lastError) {
						echo "<script type=\"text/javascript\" language=\"javascript\">updateStatus(".$i.", '".$lastError."');</script>".$nn;
					} elseif ($file["bytesReceived"] == $file["bytesTotal"] || $file["size"] == "Unknown") {
						echo "<script type=\"text/javascript\" language=\"javascript\">updateStatus(".$i.", '100%');resetProgress();</script>".$nn;
						write_file(CONFIG_DIR."files.lst", serialize(array("name" => $file["file"], "size" => $file["size"], "date" => time(), "link" => $_GET["link"], "comment" => str_replace("\n", "\\n", str_replace("\r", "\\r", $_GET["comment"]))))."\r\n", 0);
						$hideDiv = true;
					} else {
						echo "<script type=\"text/javascript\" language=\"javascript\">updateStatus(".$i.", 'Connection lost');</script>".$nn;
					}
				}
				while ($redirectto && !$lastError);
				echo "</div>".$nn;
				if ($hideDiv) {
					echo "<script type=\"text/javascript\" language=\"javascript\">document.getElementById('progress".$i."').style.display='none';</script>".$nn;
				}
			}
		}
		exit;
	} else {
		

		$start_link='index.php?audl=doum';
	
		if(isset($_REQUEST['useproxy']) && $_REQUEST['useproxy'] && (!$_REQUEST['proxy'] || !strstr($_REQUEST['proxy'], ":"))) {
	       	die('<span style="color:red; background-color:#fec; padding:3px; border:2px solid #FFaa00"><b>Not address of the proxy server is specified</b></span><br>');
	   	} else {
	   		if ($_REQUEST['useproxy'] == "on") {
				$start_link.='&proxy='.$_REQUEST['proxy'];
				$start_link.='&proxyuser='.$_REQUEST['proxyuser'];
				$start_link.='&proxypass='.$_REQUEST['proxypass'];
			}
	   	}
	
		$start_link.='&imageshack_tor='.$_REQUEST['imageshack_acc'].'&premium_acc='.$_REQUEST['premium_acc'];
		if (isset($_POST['premium_user'])) {
			$start_link.='&premium_user='.urlencode($_POST['premium_user']).'&premium_pass='.urlencode($_POST['premium_pass']);
		}
		
?>
<script type="text/javascript">

	var current_dlink=-1;
	var links = new Array();
	var start_link='<?php echo $start_link; ?>';

	function startauto() {
		current_dlink=-1;
		document.getElementById('auto').style.display='none';
		nextlink();
	}

	function nextlink() {
		if (document.getElementById('status'+current_dlink))
			document.getElementById('status'+current_dlink).innerHTML='Finished';
		current_dlink++;

		if (current_dlink < links.length) {
			document.getElementById('status'+current_dlink).innerHTML='Started';
			opennewwindow(current_dlink);
		}
	}

	function opennewwindow(id) {
		window.frames["idownload"].location = start_link+'&link='+links[id];
	}
	function addLinks() {
		var tbody = document.getElementById("links").getElementsByTagName("tbody")[0];
		var stringLinks = document.getElementById("addlinks").value;
		var regexRN = new RegExp('\r\n',"g");
		var regexN = new RegExp('\n',"g");
		var stringLinksN = stringLinks.replace(regexRN, "\n");
		var arrayLinks = stringLinksN.split(regexN);
		for (var i = 0; i < arrayLinks.length; i++)
		{
			var row = document.createElement("tr");
			var td1 = document.createElement("td");
			td1.appendChild(document.createTextNode(arrayLinks[i]));
			var td2 = document.createElement("td");
			td2.appendChild(document.createTextNode("Waiting"));
			td2.setAttribute("id", "status"+links.length);
			row.appendChild(td1);
			row.appendChild(td2);
			tbody.appendChild(row);

			links[links.length] = arrayLinks[i];
		}
		document.getElementById("addlinks").value = "";
	}
<?php
		
		for ($i=0; $i<count($getlinks); $i++)
			{
				echo "\tlinks[".$i."]='".urlencode($getlinks[$i])."';\n";
			}
?>
</script>

<table id="links" width=90% style="border:1px solid #666" class="container" cellspacing="1">
<thead><tr><td width=80% align="left"><b>Link</b></td><td width=70 align="left"><b>Status</b></td></tr></thead>
<tfoot><tr id=auto><td colspan=2 align=center><input type=button value='Start auto Transload' onClick=javascript:startauto();></td></tr></tfoot>
<tbody>
<?php
		for ($i=0; $i<count($getlinks); $i++)
			{
				echo "<tr><td nowrap>".$getlinks[$i]."</td><td id=status".$i.">Waiting</td></tr>\r\n";
			}
?>
</tbody>
</table>
<br />
<iframe width="90%" height="300" src="" name="idownload" border="1">Frames not supported, update your browser</iframe>
<br />
<table style="border:1px solid #666" class="container" cellspacing="1">
<tr>
<td><textarea name="addlinks" id="addlinks" cols="100" rows="5"></textarea></td>
<td><input type="button" value="Add links" onclick="javascript:addLinks();" /></td>
</tr>
</table>
</body>
</html>
<?php
		
		
		
		exit;
	}
}
?>
<script language=javascript>
	function ViewPage(page)
		{
			document.getElementById('listing').style.display='none';
			document.getElementById('options').style.display='none';
			document.getElementById(page).style.display='block';
		}
	function HideAll()
		{
			document.getElementById('entered').style.display='none';
			document.getElementById('worked_frame').style.display='block';
		}
</script>
<table style="border:1px solid #666" cellspacing=0 cellpadding=1 id=entered bgcolor="#001825"><tr><td>
<form action=?GO=GO method=post >
<table width=700 border=0>
<tr id=menu><td width=700 align=center>
<a href=javascript:ViewPage('listing');>Links</a>&nbsp;|&nbsp;<a href=javascript:ViewPage('options');>Options</a>
</td></tr>
<tr> <td width=100% valign=top>
<div id=listing style="display:block;">
<table border=0 style="width:710px;">
<tr><td><textarea id=links name=links rows=15 cols=60 style="width:600px; height:400px; border:1px solid #002E43"></textarea></td><td valign=top><input type=submit value="Transload files" onClick=javascript:HideAll(); style="width:100px;"></tr>
</table>
</div>
<div width=100% id="options" style="display:none;">
    <table cellspacing="5" style="width:710px;">
      <tbody>
      <tr>
        <td align="center">

          <table align="center">
            <tr>
              <td>
                <input type="checkbox" id=useproxy name=useproxy onClick="javascript:var displ=this.checked?'':'none';document.getElementById('proxy').style.display=displ;"<?php echo $_COOKIE["useproxy"] ? " checked" : ""; ?>>&nbsp;Use Proxy Settings
              </td>
              <td>&nbsp;

              </td>
              <td id=proxy<?php echo $_COOKIE["useproxy"] ? "" : " style=\"display: none;\""; ?>>
                <table border=0>
                  <tr><td>Proxy:</td><td><input name=proxy size=25<?php echo $_COOKIE["proxy"] ? " value=\"".$_COOKIE["proxy"]."\"" : ""; ?>></td></tr>
                  <tr><td>UserName:</td><td><input name=proxyuser size=25 <?php echo $_COOKIE["proxyuser"] ? " value=\"".$_COOKIE["proxyuser"]."\"" : ""; ?>></td></tr>
                  <tr><td>Password:</td><td><input name=proxypass size=25 <?php echo $_COOKIE["proxypass"] ? " value=\"".$_COOKIE["proxypass"]."\"" : ""; ?>></td></tr>
                </table>
              </td>
            </tr>
            <tr>
              <td>
              </td>
            </tr>
			<tr>
			<td>
                <input type="checkbox" value="on" name=imageshack_acc id=imageshack_acc <?php if (is_array($imageshack_acc)) print ' checked'; ?>>&nbsp;Use Imageshack Account
              </td>
			</tr>
            <?php
            if ($maysaveto === true)
                    {
            ?>
            <tr>
              <td>
                <input type="checkbox" name=saveto id=saveto onClick="javascript:var displ=this.checked?'':'none';document.getElementById('path').style.display=displ;"<?php echo $_COOKIE["saveto"] ? " checked" : ""; ?>>&nbsp;Save To
              </td>
              <td>&nbsp;

              </td>
              <td id=path <?php echo $_COOKIE["saveto"] ? "" : " style=\"display: none;\""; ?> test>
                Path:&nbsp;<input name=savedir size=30 value="<?php echo realpath(($_COOKIE["savedir"] ? $_COOKIE["savedir"] : (strstr(realpath("./"), ":") ? addslashes($workpath) : $workpath))) ?>">
              </td>
            </tr>
            <?php
                    }
            ?>

			<tr>
			<td><input type="checkbox" name="premium_acc" id="premium_acc" onClick="javascript:var displ=this.checked?'':'none';document.getElementById('premiumblock').style.display=displ;"<?php if (count($premium_acc) > 0) print ' checked'; ?>>&nbsp;Use Premium Account</td>
			<td>&nbsp;</td>
			<td id="premiumblock" style="display: none;">
			<table width="150" border="0">
			<tr><td>Username:&nbsp;</td><td><input type="text" name="premium_user" id="premium_user" size="15" value=""></td></tr>
			<tr><td>Password:&nbsp;</td><td><input type="password" name="premium_pass" id="premium_pass" size="15" value=""></td></tr>
			</table>
			</td>
			</tr>
			<tr>
			<td><input type="checkbox" name="server_side" value="on" />Run Server Side</td></tr>
          </table>
        </td>
      </tr>
      </tbody>
    </table>
</div>
</td></tr>
</table>
</form>
</td></tr></table>
</center>
</body>
</html>