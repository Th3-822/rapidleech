<?php

$ref = "http://rapidshare.com";
$page = geturl("www.rapidshare.com", 80, "/", "", $cookie, 0, 0, "");
$action_url=cut_str($page,'<form name="ul" method="post" action="','" enctype=');
$url = parse_url($action_url);
$post["u"] = "";
$upfiles=upfile($url["host"],defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""),$ref, $cookie, $post, $lfile, $lname, "filecontent");
$download_link = cut_str($upfiles,'<div class="downloadlink">',"</div>");
?>