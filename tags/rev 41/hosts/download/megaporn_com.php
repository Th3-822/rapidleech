<?php

$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth);   
preg_match('/ocation: (.*)/',$page,$loc);
$link = trim($loc[1]);
preg_match_all('/[^=]*/',$link,$match);
$codl = $match[0][2];
if (!$codl){html_error("bud link","0");}
$lkvid="http://www.megaporn.com/video/xml/videolink.php?v=".$codl;
$url=parse_url($lkvid);
$xml = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""), $Referer, $cookie, 0, 0, $_GET["proxy"],$pauth); 
$xmld=urldecode($xml);
$vs=cut_str($xml,'" s="','"');
$vun=cut_str($xml,'" un="','"');  
$vk1=cut_str($xml,'" k1="','"');  
$vk2=cut_str($xml,'" k2="','"');
$FileName=cut_str($xml,'" title="','"').".flv";
if ($FileName ==""){
   $FileName=basename($dwn).".flv";
} 
$id=ddm($vun,$vk1,$vk2); 
$dwn="http://www".$vs.".megaporn.com/video/files/".$id."/";
$Url = parse_url($dwn);
insert_location ( "$PHP_SELF?filename=" .  ( $FileName ) . "&host=" . $Url ["host"] ."&port=".$Url["port"]."&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&cookie=" . urlencode ( $cookie ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . ($pauth ? "&pauth=$pauth" : "").(isset($_GET["audl"]) ? "&audl=doum" : "") );
function ddm($str, $key1, $key2){$_loc1 = array();for ($_loc3 = 0; $_loc3 < strlen($str); ++$_loc3){switch ($str{$_loc3}){case "0":{$_loc1[]="0000";break;}case "1":{$_loc1[]="0001";break;}case "2":{$_loc1[]="0010";break;}case "3":{$_loc1[]="0011";break;}case "4":{$_loc1[]="0100";break;}case "5":{$_loc1[]="0101";break;}case "6":{$_loc1[]="0110";break;}case "7":{$_loc1[]="0111";break;}case "8":{$_loc1[]="1000";break;}case "9":{$_loc1[]="1001";break;}case "a":{$_loc1[]="1010";break;}
case "b":{$_loc1[]="1011";break;}case "c":{$_loc1[]="1100";break;}case "d":{$_loc1[]="1101";break;}case "e":{$_loc1[]="1110";break;}case "f":{$_loc1[]="1111";break;}} } $_loc1 = join("",$_loc1);$_loc1 = str_split($_loc1);$_loc6 = array();$kx = 0;for ($_loc3 = 0; $_loc3 < 384; ++$_loc3){$key1 = ($key1 * 11 + 77213) % 81371;$key2 = ($key2 * 17 + 92717) % 192811;$_loc6[$_loc3] = ($key1 + $key2) % 128;} for ($_loc3 = 256; $_loc3 >= 0; --$_loc3){$_loc5 = $_loc6[$_loc3];$_loc4 = $_loc3 % 128;
$_loc8 = $_loc1[$_loc5];$_loc1[$_loc5] = $_loc1[$_loc4];$_loc1[$_loc4] = $_loc8;} for ($_loc3 = 0; $_loc3 < 128; ++$_loc3){$_loc1[$_loc3] = $_loc1[$_loc3] ^ $_loc6[$_loc3 + 256] & 1;} $_loc12 = join($_loc1,"");$_loc7 = array();for ($_loc3 = 0; $_loc3 < strlen($_loc12); $_loc3 = $_loc3 + 4){$_loc9 = substr($_loc12,$_loc3, 4);$_loc7[]=$_loc9;} $_loc2 = array();for ($_loc3 = 0; $_loc3 < count($_loc7); ++$_loc3){switch ($_loc7[$_loc3]){case "0000":{$_loc2[]="0";break;}case "0001":{$_loc2[]="1";break;}
case "0010":{$_loc2[]="2";break;}case "0011":{$_loc2[]="3";break;}case "0100":{$_loc2[]="4";break;}case "0101":{$_loc2[]="5";break;}case "0110":{$_loc2[]="6";break;}case "0111":{$_loc2[]="7";break;}case "1000":{$_loc2[]="8";break;}case "1001":{$_loc2[]="9";break;}case "1010":{$_loc2[]="a";break;}case "1011":{$_loc2[]="b";break;}case "1100":{$_loc2[]="c";break;}case "1101":{$_loc2[]="d";break;}case "1110":{$_loc2[]="e";break;}case "1111":{$_loc2[]="f";break;}} } return (join($_loc2,""));} 

/*
megavideo download plug-in writted by kaox 25/04/09
*/

?>
