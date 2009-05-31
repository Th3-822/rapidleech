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
require_once("configs/config.php");
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
if ($_REQUEST["GO"] == "GO")
	{
		$getlinks=explode("\r\n",trim($_REQUEST[links]));
		
		if (!count($getlinks) || (trim($_REQUEST[links]) == ""))
			{
				die('<span style="color:red; background-color:#fec; padding:3px; border:2px solid #FFaa00"><b>Not LINK</b></span><br>');
			}
			

		$start_link='index.php?';

		if(isset($_REQUEST[useproxy]) && $_REQUEST[useproxy] && (!$_REQUEST[proxy] || !strstr($_REQUEST[proxy], ":")))
		    {
	        	die('<span style="color:red; background-color:#fec; padding:3px; border:2px solid #FFaa00"><b>Not address of the proxy server is specified</b></span><br>');
	    	}
	    		else
	    	{
	    		if ($_REQUEST[useproxy] == "on")
	    			{
						
						$start_link.='&proxy='.$_REQUEST[proxy];
						$start_link.='&proxyuser='.$_REQUEST[proxyuser];
						$start_link.='&proxypass='.$_REQUEST[proxypass];
					}
	    	}

		$pre_user = $_REQUEST[rrapidlogin_com] ? $_REQUEST[rrapidlogin_com] : $premium_acc["au_dl"]["user"];
		$pre_pass = $_REQUEST[rrapidpass_com] ? $_REQUEST[rrapidpass_com] : $premium_acc["au_dl"]["pass"];
		
		$start_link.='&imageshack_tor='.$_REQUEST[imageshack_acc].'&premium_acc='.$_REQUEST[rapidpremium_com];
		if ($_REQUEST[rapidpremium_com] == "on") 
			{
			$start_link.='&premium_user='.$pre_user;
			$start_link.='&premium_pass='.$pre_pass;
			}
		
?>
<script language="javascript">

	var set_delay=0;
	var current_dlink=-1;
	var last_status = new Array();
	var links = new Array();
	var idwindow = new Array();
	var dwindow = '<?php echo '_'.substr(md5(time()),0,7).'_'; ?>';
	var start_link='<?php echo $start_link; ?>';

	function download(id)
		{
			opennewwindow(id);
		
			document.getElementById('auto').style.display='none';
			document.getElementById('dButton'+id).style.display='none';
		}
	
	function startauto()
		{
			var delay_=document.getElementById('delay').value;
			if (!((delay_>=1) && (delay_<=3600)))
				{
					alert('Errors in the interval of delay (from 1 to 3600 seconds)');
					return;
				}
				
			set_delay=delay_*1000;
		
			current_dlink=-1;
			document.getElementById('auto').style.display='none';
			
			for(var i=0; i<links.length; i++)
				{
					document.getElementById('dButton'+i).style.display='none';
					document.getElementById('status'+i).innerHTML='&nbsp;Wait';
					
				}
				
			nextlink();
		}
		
	function nextlink()
		{
			current_dlink++;
			
			document.getElementById('status'+current_dlink).innerHTML='&nbsp;Started';
			if (current_dlink < links.length)
				{
					opennewwindow(current_dlink);
					setTimeout('nextlink()',set_delay);
				}
		}

	function opennewwindow(id)
		{
			var options = "width=700,height=450,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,copyhistory=no";
			idwindow[id] = window.open(start_link+'&link='+links[id], dwindow+id, options);
			idwindow[id].opener=self;
			idwindow[id].focus();
		}
	
<?php
		
		for ($i=0; $i<count($getlinks); $i++)
			{
				echo "\tlast_status[$i]=''; links[".$i."]='".urlencode($getlinks[$i])."';\n";
			}
?>
</script>

<table width=90% style="border:1px solid #666" class="container" cellspacing="1">
<tr><td width=80% align="center"><b>Link</b><td width=70 align="center">&nbsp;<b>Action</b>&nbsp;<td width=70 align="center">&nbsp;<b>Status</b>&nbsp;</tr>
<?php
		for ($i=0; $i<count($getlinks); $i++)
			{
				echo "<tr><td width=80% nowrap id=row".$i.">".$getlinks[$i]."</td>";
				echo "<td width=70 id=action".$i."><input type=button onClick=javascript:download($i); value='Transload' id=dButton".$i."></td>";
				echo "<td width=70 id=status".$i.">&nbsp;</td>";
				echo "</tr>\n";
			}
?>
<tr id=auto><td colspan=3 align=center>Intervals (1 ... 3600)&nbsp;<input type=text id=delay name=delay size=5 value=20>&nbsp;seconds&nbsp;<input type=button value='Start auto Transload' onClick=javascript:startauto();></tr>
</table>
<?php
		
		
		
		exit;
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

            <?php
            if (!$rapidlogin_com || !$rapidpass_com)
            	{
            ?>
            <tr>
              <td>
                <input type="checkbox" value="on" name=rapidpremium_com id=rapidpremium_com onClick="javascript:var displcom=this.checked?'':'none';document.getElementById('rapidblockcom').style.display=displcom;" <?php if (is_array($premium_acc["au_dl"])) print ' checked'; ?>>&nbsp;Use Premium Account
              </td>
              <td>&nbsp;</td>
              <td id=rapidblockcom<?php echo $_COOKIE["rapidpremium_com"] ? "" : " style=\"display: none;\""; ?>>
                <table width=150 border=0>
                 <tr><td>Username</td><td><input type=text name=rrapidlogin_com size=15 value="<?php echo ($_COOKIE["rrapidlogin_com"] ? $_COOKIE["rrapidlogin_com"] : ""); ?>"></td></tr>
                 <tr><td>password</td><td><input type=password name=rrapidpass_com size=15 value="<?php echo ($_COOKIE["rrapidpass_com"] ? $_COOKIE["rrapidpass_com"] : ""); ?>"></td></tr>
                </table>
              </td>
            </tr>
            <?php
            	}
            ?>
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