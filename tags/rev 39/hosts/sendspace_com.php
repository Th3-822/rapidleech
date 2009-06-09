<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }

//Use PREMIUM? [szalinski 09-May-09]
if (($_GET["premium_acc"] == "on" && $_GET["premium_user"] && $_GET["premium_pass"]) || ($_GET["premium_acc"] == "on" && $premium_acc["sendspace"]["user"] && $premium_acc["sendspace"]["pass"]))
{
	function biscotti($content)
	{
		is_page($content);
		preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
		foreach ($matches[0] as $coll)
		{
			$bis.=cut_str($coll,"Set-Cookie: ","; ")."; ";
		}
	return $bis;
	}

	$page = geturl("sendspace.com", 80, "/", "", 0, 0, 0, "");
	$cook=biscotti($page);
	$post["action"] = "login";
	$post["username"] = $_GET["premium_user"] ? trim($_GET["premium_user"]) : $premium_acc["sendspace"]["user"];
	$post["password"] = $_GET["premium_pass"] ? trim($_GET["premium_pass"]) : $premium_acc["sendspace"]["pass"];
	$post["remember"] = '1';
	$post["submit"] = "login";
	$post["openid_url"] = "";
	$post["action_type"] = "login";
	$page=geturl("sendspace.com", 80, "/login.html", "http://sendspace.com/login.html", $cook, $post, 0, $_GET["proxy"]);
	$cook=$cook." ".biscotti($page);
	is_present($cook,"ssal=deleted","Login incorrect retype your username or password correctly");
	$Url = parse_url($LINK);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, $cook, 0, 0, $_GET["proxy"], $pauth);
	preg_match('%(http://fs.+\.sendspace\.com/dlp/[a-f0-9]{32}/.{8}/.{6}/.{6}/[^/]+\..{3})"\s%', $page, $dlink);
	$Url = parse_url($dlink[1]);
	$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, $cook, 0, 0, $_GET["proxy"], $pauth);
	preg_match('%ocation: (.+)\r\n%', $page, $flink);
	$Href = 'http://' . $Url['host'] . $flink[1];
	$Url = parse_url($Href);
	$FileName = basename($Url['path']);
	insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($LINK)."&cookie=".urlencode($cook)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".$LINK.($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "").($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
}
else
//Use FREE instead?
{
	function sendspace_enc($par1,$par2,$text)
  {
  $myarr = array();

  for ($i = 0; $i < $par1; $i++)
    {
		$myarr[$i] = $i;
    }
	
  for ($j = 0,$k = $j,$l = $myarr; $j < $par1; $j++)
    {
		$k = (ord($par2[$j%strlen($par2)])+$l[$j]+$k)%$par1;
		$m = $l[$j];
		$l[$j] = $l[$k];
		$l[$k] = $m;
		$l[$k] = $l[$k]^5;
    }

  for ($res = '', $k = 0,$n = 0;$n < strlen($text); $n++)
    {
		$o = $n%$par1;
		$k = ($l[$o]+$k)%$par1;
		$p = $l[$o];
		$l[$o] = $l[$k];
		$l[$k] = $p;
		$res.= chr(ord($text[$n])^$l[($l[$o]+$l[$k])%$par1]);
    }

  return $res;
  }

function sendspace_base64ToText($t)
  {
	$b64s = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_"';
  $r = '';
  $m = 0;
  $a = 0;
  $l = strlen($t);
    
  for($n = 0; $n<$l; $n++)
    {
    $c = strpos($b64s,$t[$n]);
    if($c >= 0)
      {
      if($m)
       	{
       	$d = ($c << (8-$m))& 255 | $a;
        $r.= chr($d);
        }
      $a = $c >> $m;
      $m+= 2;
      if($m == 8)
        {
        $m = 0;
        }
      }
    }
    
  return $r;
  }
}
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], 0, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);
				
is_present($page,"There are no free download slots available");
is_present($page,"Sorry, the file you requested is not available");
//$countDown=trim(cut_str($page,"var count = ",";"));
//insert_timer($countDown, "File is being prepared.","",true);

$code_enc = cut_str($page,'function enc(text){','}</script>');
if (!$code_enc)
	{
	html_error('Error getting link');
	}
				
$par1 = cut_str($code_enc,'Array();',';');
list($tmp,$par1) = explode('=',$par1);
				
$par2 = cut_str($code_enc,"='","';");
				
$dec_text = cut_str($page,"enc(base64ToText('","')));");
				
$d64text = sendspace_base64ToText($dec_text);
$urlnew = sendspace_enc($par1,$par2,$d64text);

is_notpresent($urlnew,'href="','Error decrypting URL page');
				
$Href = cut_str($urlnew,'href="','" onclick');
if (!$Href)
	{
	html_error('Error decrypting URL page');
	}

$Url = parse_url($Href);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
if(preg_match('/location: (.*)/im', $page, $loc)){
	$Href = 'http://'.$Url["host"].$loc[1];
}
$Url = parse_url($Href);
//$FileName = !$FileName ? basename($Url["path"]) : $FileName;
$FileName = !$FileName ? basename(trim($loc[1])) : $FileName;

insert_location("$PHP_SELF?filename=".urlencode($FileName)."&host=".$Url["host"]."&path=".urlencode($Url["path"].($Url["query"] ? "?".$Url["query"] : ""))."&referer=".urlencode($Referer)."&email=".($_GET["domail"] ? $_GET["email"] : "")."&partSize=".($_GET["split"] ? $_GET["partSize"] : "")."&method=".$_GET["method"]."&proxy=".($_GET["useproxy"] ? $_GET["proxy"] : "")."&saveto=".$_GET["path"]."&link=".urlencode($LINK).($_GET["add_comment"] == "on" ? "&comment=".urlencode($_GET["comment"]) : "")."&auth=".$auth.($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : ""));
?>