<?php
####### Account Info. ###########
$dailymotion_login = '';
$dailymotion_pass = '';
##############################

$not_done = true;
$continue_up = false;
if ($dailymotion_login && $dailymotion_pass) {
    $_REQUEST['my_login'] = $dailymotion_login;
    $_REQUEST['my_pass'] = $dailymotion_pass;
    $_REQUEST['action'] = "FORM";
    echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up = true;
else {
    ?>
    <table border=0 style="width:270px;" cellspacing=0 align=center>
        <form method=post>
            <input type=hidden name=action value='FORM' />
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
            <tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
            <tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["dailymotion.com"]; ?></b></small></tr>
    </table>
    </form>

    <?php
}

if ($continue_up) {
    $not_done = false;
    ?>
    <table width=600 align=center>
    </td></tr>
    <tr><td align=center>
            <div id=login width=100% align=center>Login to Dailymotion.com</div>
    <?php
    $rnd = time() . rndNum(3);
    $post = array();
    $post['form_name'] = "dm_pageitem_login";
    $post['username'] = trim($_REQUEST['my_login']);
    $post['password'] = trim($_REQUEST['my_pass']);
    $post['login_submit'] = "Login";
    $page = geturl("www.dailymotion.com", 80, "/login", "http://www.dailymotion.com/us", 0, $post, 0);
    is_page($page);
    is_present($page, "Wrong login data!", "Error logging in - are your logins correct!", "0");
    $cookie = GetCookies($page);
    $page = geturl("www.dailymotion.com", 80, "/upload", "http://www.dailymotion.com/us", $cookie, 0, 0);
    is_page($page);
    $rand = time();
    $cookie .= "; " . GetCookies($page) . "; " . "dmuat=$rand ";
    unset($post);
    $post["ajax_function"] = "create";
    $post["ajax_arg[]"] = urlencode($serv . "/upload#hp-h-10");
    $post["ajax_arg[]"] = urlencode($lname);
    $post["ajax_rnd"] = $rnd;
    $post["from_request"] = "%2Fupload";
    $page = geturl("www.dailymotion.com", 80, "/ajax/video", "http://www.dailymotion.com/upload\r\nX-Requested-With: XMLHttpRequest", $cookie, $post, 0);
    is_page($page);
    $vid = cut_str($page, 'video_id":', '}');
    $rnt = "0." . rndNum(17);
    $page = geturl("www.dailymotion.com", 80, "/pageitem/upload/file?request=%2F&t=$rnt&from_request=%2Fupload&loop=0", "http://www.dailymotion.com/upload\r\nX-Requested-With: XMLHttpRequest", $cookie, 0, 0);
    is_page($page);
    ?>
            <script>document.getElementById('login').style.display='none';</script>
            <div id=info width=100% align=center>Retrive upload ID</div>
            <?php
            $upurl = cut_str($page, 'xuploadUrl = \"', '"');
            $upurl = htmlspecialchars_decode($upurl);
            $upurl = str_replace("\\", "", $upurl);
            $url = parse_url($upurl);
            unset($post);
            $post = array();
            $post["Filename"] = $lname;
            $post["Upload"] = 'Submit Query';
            $upfiles = upfile($url["host"], defport($url), $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 0, 0, $post, $lfile, $lname, "file");
            is_page($upfiles);
            ?>
            <script>document.getElementById('progressblock').style.display='none';</script>
            <?php
            is_page($upfiles);
            $format = urlencode(cut_str($upfiles, 'format":"', '"'));
            $acodec = urlencode(cut_str($upfiles, 'acodec":"', '"'));
            $vcodec = urlencode(cut_str($upfiles, 'vcodec":"', '"'));
            $duration = cut_str($upfiles, 'duration":', ',');
            $bitrate = cut_str($upfiles, 'bitrate":', ',');
            $dimension = cut_str($upfiles, 'dimension":"', '"');
            $name = urlencode(cut_str($upfiles, 'name":"', '"'));
            $size = cut_str($upfiles, 'size":', ',');
            $ulink = cut_str($upfiles, 'url":"', '"');
            $uurl = urlencode($ulink);
            $hash = cut_str($upfiles, 'hash":"', '"');
            $seal = cut_str($upfiles, 'seal":"', '"');

            $daturl = "http://www.dailymotion.com/ajax/getVideoFromXUpload?xupload_response=1&video_id=$vid&formUpload=upload_0&format=$format&acodec=$acodec&vcodec=$vcodec&duration=$duration&bitrate=$bitrate&dimension=$dimension&name=$name&size=$size&url=$uurl&hash=$hash&seal=$seal";
            $Url = parse_url($daturl);
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.dailymotion.com/upload\r\nX-Requested-With: XMLHttpRequest", $cookie, 0, 0, $_GET["proxy"], $pauth);
            is_page($page);
            if ($error = cut_str($page, 'message":"', '",')) {
                html_error("$error");
            }
            $rng = "0." . rndNum(16);
            $Url = parse_url("http://www.dailymotion.com/pageitem/video/edit?request=%2F&t=$rng&from_request=%2Fupload&loop=0");
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.dailymotion.com/upload", $cookie, 0, 0, $_GET["proxy"], $pauth);
            $latitude = cut_str($page, 'name="latitude" value="', '"');
            $longitude = cut_str($page, 'name="longitude" value="', '"');
            $title = $name;
            $save = cut_str($page, 'name="save" value="', '"');
            $recordedOn=cut_str($page, 'id="more_details_0_recordedOn" value="', '"');
            $dpost=array(
                "form_name"=>"dm_pageitem_video_edit_0",
                "video_title"=>$title,
                "user_category"=>"shortfilms",
                "tags"=>"clip " . $title,
                "description"=>"Upload by rapidleech",
                "privacy"=>"public",
                "allow_comments"=>"1",
                "allow_in_group"=>"1",
                "language"=>"vi",
                "recordedOn"=>$recordedOn,
                "save_geolocation"=>0,
                "is_remove_geoloc"=>0,
                "latitude"=>$latitude,
                "longitude"=>$longitude,
                "coming_next"=>"",
                "videoId"=>$vid,
                "uploadType"=>"file",
                "save"=>$save,
            );            
            $Url = parse_url("http://www.dailymotion.com/pageitem/video/edit?request=%2F&loop=0");
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.dailymotion.com/upload", $cookie, $dpost, 0, $_GET["proxy"], $pauth);
            is_page($page);
            $Url = parse_url("http://www.dailymotion.com/ajax/video");
            unset($post);
            $post = array(
                "ajax_function" => "get_url",
                "ajax_arg[]" => $vid,
                "ajax_rnd" => round(microtime(true) * 1000),
                "from_request" => "%2Fupload"
            );
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.dailymotion.com/upload", $cookie, $post, 0, $_GET["proxy"], $pauth);
            is_page($page);
            $linkul = cut_str($page, "+:", "\n");
            $Url = parse_url("http://www.dailymotion.com/ajax/video");
            unset($dpost);
            $dpost = array(
                "ajax_function" => "generate_vs_upload_tag",
                "ajax_arg[]" => "Save",
                "ajax_arg[]" => "1",
                "ajax_rnd" => round(microtime(true) * 1000),
                "from_request" => "%2Fupload"
            );
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.dailymotion.com/upload\r\nX-Requested-With: XMLHttpRequest\r\nX-Prototype-Version: 1.7", $cookie, $dpost, 0, $_GET["proxy"], $pauth);
            is_page($page);
            echo "<h3><font color='green'>File successfully uploaded to your account</font></h3>";                        
            $download_link = "http://www.dailymotion.com" . $linkul;
        }

        function rndNum($lg) {
            $str = "0123456789";
            for ($i = 1; $i <= $lg; $i++) {
                $st = rand(1, 9);
                $pnt.=substr($str, $st, 1);
            }
            return $pnt;
        }

//VinhNhaTrang 19/12/2010
//fix by vdhdevil 14/10/2011
        ?>