<?php
if (!defined('RAPIDLEECH'))
  {
  require_once("index.html");
  exit;
  }
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
preg_match('/ocation: (.*)/',$page,$loc);
$loc = trim($loc[1]);
$Url = parse_url($loc);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
$FileName = basename($Url["path"]);
$loc = "{$_SERVER['PHP_SELF']}?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] . "&port=" . $Url ["port"] . "&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $link ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . $auth . ($pauth ? "&pauth=$pauth" : "") . "&cookie=" . urlencode($cookie) . "&post=" . urlencode ( serialize ( $post ) ).(isset($_GET["audl"]) ? "&audl=doum" : "");
insert_location ( $loc );
exit;
?>