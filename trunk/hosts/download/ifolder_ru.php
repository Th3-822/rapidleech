<?php
$ifr = $_POST['ifr'];
if(!$ifr == "ok"){	
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, 0, 0, 0, $_GET["proxy"],$pauth);
	$cook=biscottiDiKaox($page);
	is_page($page);
	$fid=basename($LINK);
	is_present($page,"Файл номер <b>$fid</b> не найден !!!",'Request file not found');
	is_present($page,"Файл номер <b>$fid</b> удален",'Request file deleted');
	$session_id=trim(cut_str($page,'name="session" value="','"'));
	$file_id=trim(cut_str($page,'name="file_id" value="','"'));
	$tmphc=cut_str($page,"'hidden_code'","</script>");
	$hidden_code=cut_str($tmphc,"'","'");
	$ss=cut_str($tmphc,'.substring(',')');
	if (is_numeric($ss)) $hidden_code=substr($hidden_code,$ss);
	$name=cut_str($page,'Название: <b>','</b>');
	$size=cut_str($page,'Размер: <b>','</b>');
		if (strstr($page,'Владелец файла установил пароль для скачивания'))
		{
			$pass_request=true;
			if (!$_REQUEST["password"]) { html_error('File is protected via password'); }
		}
	if (strpos($page,'ints.ifolder')!==false) {
	$pageq = geturl("ints.ifolder.ru", defport($Url), "/ints/?".$Url["host"].$Url["path"]."?ints_code=", 0, $cook, 0, 0, $_GET["proxy"],$pauth);
	$pageq = "http://s.agava.ru".cut_str($pageq,"http://s.agava.ru",">");
	$pageq = parse_url($pageq);
	$pageq=geturl($pageq["host"], defport($Url), $pageq["path"]."?".$pageq["query"], 0, $cook, 0, 0, $_GET["proxy"],$pauth);
	$pageq=cut_str($pageq,'<A HREF="','"');
	$gurl = parse_url($pageq);
	$pageq=geturl($gurl["host"], defport($Url), $gurl["path"]."?".$gurl["query"], 0, $cook, 0, 0, $_GET["proxy"],$pauth);
	$stat1.=$pageq;
	$session_id=cut_str($gurl["query"],'session=','&');
	geturl("ints.ifolder.ru", defport($Url), "/ints/frame/?session=$session_id", 0, $cook, 0, 0, $_GET["proxy"],$pauth);
	insert_timer(32, "Included additional advertising.");	
	$page = geturl("ints.ifolder.ru", defport($Url), "/ints/frame/?session=$session_id", 0, $cook, 0, 0, $_GET["proxy"],$pauth);
	is_page($page);
	$page=$page;
	$LINK="http://ints.ifolder.ru/ints/frame/?session=$session_id";
	$Referer=$LINK;
	$Url["host"]="ints.ifolder.ru";
	$tag_session=cut_str($page,'tag.value = "','"');
	$asd=cut_str($page,'"+"name=\'','\'');
	}
	$access_image_url='http://'.$Url["host"].'/random/images/?session='.$session_id."&mem";	
    $gurl=parse_url($access_image_url);
    $page=geturl($gurl["host"], defport($Url), $gurl["path"]."?".$gurl["query"], 0, $cook, 0, 0, $_GET["proxy"],$pauth);
    	$headerend = strpos($page,"GIF");
		$pass_img = substr($page,$headerend);
        $imgfile=$download_dir."ifolder_captcha.gif";
//	    if (file_exists($imgfile)) unlink($imgfile);
//		write_file($imgfile, $pass_img);
		$fimg= fopen($imgfile,"w");
         fwrite($fimg,$pass_img);
         fclose($fimg);
	
	echo "<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	echo "<b>Please enter code:</b><br>$nn";
	echo "<img src=\"$imgfile\">$nn";	
	echo "<input type=hidden name=file_id value='$file_id'>\n";
	echo "<input type=hidden name=session_id value='$session_id'>\n";
	echo "<input type=hidden name=hidden_code value='$hidden_code'>\n";
	echo "<input type=hidden name=tag_session value='$tag_session'>\n";
	echo "<input type=hidden name=asd value='$asd'>\n";
	echo "<input name=\"accesscode\" type=\"text\" >";
    echo "<input type=hidden name=cook value='$cook'>\n"; 
	echo "<input type=hidden name=link value='$LINK'>\n";
	echo "<input type=hidden name=ifr value='ok'>\n";
	echo "<input type=hidden name=name value='".urlencode($name)."'>\n";
	echo "<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";

	}else{
	
	// 2°
	$post["confirmed_number"]=$_REQUEST[accesscode];
	$post["session"]=$_REQUEST[session_id];
	$post["action"]="1";
	if ($_REQUEST[hidden_code]) $post["hidden_code"]=$_REQUEST[hidden_code];
	if ($_REQUEST[tag_session]) $post["tag_session"]=$_REQUEST[tag_session];
	if ($_REQUEST[asd]) $post[$_REQUEST[asd]]="1";
	if($_REQUEST[sms]) $post[$_REQUEST[sms]]=1;
    $cook=$_REQUEST[cook]; 
	$post["activate_ads_free"]=0;
	$file_id=$_REQUEST[file_id];
	$post["file_id"]=$file_id;
	if ($_REQUEST["requery_pass"] == 1) { $post["pswd"]=$_REQUEST["password"]; }
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cook, $post, 0, $_GET["proxy"],$pauth);
	$loc=cut_str($page,"Location: ","\r");
	if ($loc) {
	$Url=parse_url($loc);
	$page = geturl($Url["host"], defport($Url), $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $LINK, $cook, 0, 0, $_GET["proxy"],$pauth);}
	
	is_page($page);
	if (strstr($page,'неверный код'))
		{
		html_error('The code entered is incorrect', $head = 1);
		}

	is_present($page,'На данный момент иностранный трафик у этого файла превышает Российский.','The Foreign traffic exceeds Russian.');
	is_notpresent($page,'Ссылка для скачивания файла:','File not found or Incorrect entered cod'.$page);
	if(!preg_match("#http://[^.]+.ifolder.ru/download/[^'\"<]+#", $page, $regs)) html_error("Error get direct link");
	$Href = $regs[0];
	$Url = parse_url($Href);
	$FileName=urldecode($_REQUEST['name']);
	insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] ."&port=".$Url["port"]."&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . ($pauth ? "&pauth=$pauth" : "") );
    }
function biscottiDiKaox($content)
 {
 preg_match_all("/Set-Cookie: (.*)\n/",$content,$matches);
 foreach ($matches[1] as $coll) {
 $bis0=split(";",$coll);
 $bis1=$bis0[0]."; ";
 $bis2=split("=",$bis1);
if  (substr_count($bis,$bis2[0])>0)
{$patrn=$bis2[0]."[^ ]+"; 
$bis=preg_replace("/$patrn/",$bis1,$bis);     
} else{$bis.=$bis1 ; }}
$bis=str_replace("  "," ",$bis);     
return rtrim($bis);}
	
/*
  written by kaox 10/05/09	
 */
	
?>

