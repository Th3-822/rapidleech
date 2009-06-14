<?php

  $ylo = $_POST['ylo'];
if($ylo == "ok"){
    $post = array();
    $post["cap"] = $_POST["cap"];
    $post["_____download.x"] = "99";
    $post["_____download.y"] = "19";
    $cookie = $_POST["cookie"];
    $Referer = $_POST["link"];
    $FileName = $_POST["FileName"];
    $Url = parse_url($Referer);
    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, $cookie, $post, 0, $_GET["proxy"],$pauth);
	
    
    if(preg_match('/Location: *(.+)/', $page, $redir)){
     $redirect=rtrim($redir[1]);
    
          $Url = parse_url($redirect);
		  

insert_location ( "$PHP_SELF?filename=" . urlencode ( $FileName ) . "&host=" . $Url ["host"] ."&port=".$Url["port"]."&path=" . urlencode ( $Url ["path"] . ($Url ["query"] ? "?" . $Url ["query"] : "") ) . "&referer=" . urlencode ( $Referer ) . "&email=" . ($_GET ["domail"] ? $_GET ["email"] : "") . "&partSize=" . ($_GET ["split"] ? $_GET ["partSize"] : "") . "&method=" . $_GET ["method"] . "&proxy=" . ($_GET ["useproxy"] ? $_GET ["proxy"] : "") . "&saveto=" . $_GET ["path"] . "&link=" . urlencode ( $LINK ) . ($_GET ["add_comment"] == "on" ? "&comment=" . urlencode ( $_GET ["comment"] ) : "") . ($pauth ? "&pauth=$pauth" : "") );
}
}else{
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $Referer, 0, 0, 0, $_GET["proxy"],$pauth);
is_page($page);

if(preg_match('/design\/dynIMG\.php\?key=\w+/', $page, $cap)){
$img_link = "http://youload.to/".$cap[0];
preg_match('/\/index\.php.+?"/', $page, $linkid);
$link = "http://youload.to".$linkid[0];


if(preg_match_all('/Set-Cookie: *(.+);/', $page, $cook)){
		$cookie = implode(';', $cook[1]);
	}else{
		html_error("Cookie not found.", 0);
	}
	preg_match('/&name=.+&/', $page, $FName);
	$FileName=str_replace("name=","",$FName[0]);
	$FileName=str_replace("&","",$FileName);
	
		$Url = parse_url($img_link);
$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), $free_link, $cookie, 0, 0, $_GET["proxy"],$pauth);
		$headerend = strpos($page,"GIF89a");
		$pass_img = substr($page,$headerend);
                $imgfile=$download_dir."youload_captcha.gif";
				if (file_exists($imgfile)) unlink($imgfile);
		write_file($imgfile, $pass_img);

	print 	"<form method=\"post\" action=\"".$PHP_SELF.(isset($_GET["audl"]) ? "?audl=doum" : "")."\">$nn";
	print	"<b>Please enter code:</b><br>$nn";
	print	"<img src=\"$imgfile\">$nn";
	print	"<input name=\"ylo\" value=\"ok\" type=\"hidden\">$nn";
	print	"<input name=\"link\" value=\"$link type=\"hidden\">$nn";
	print	"<input name=\"cap\" type=\"text\" >";
	print	"<input name=\"cookie\" value=\"$cookie\" type=\"hidden\">$nn";
    print	"<input name=\"FileName\" value=\"$FileName\" type=\"hidden\">$nn";
    print	"<input name=\"Submit\" value=\"Submit\" type=\"submit\"></form>";
	
}
}
/*
written by kaox
*/

?>

