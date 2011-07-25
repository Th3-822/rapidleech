<?php
####### Account Info. ###########
$upload_acc['fshare_vn']['user'] = ""; //Set you username
$upload_acc['fshare_vn']['pass'] = ""; //Set your password
##############################

$not_done = true;
$continue_up = false;
if ($upload_acc['fshare_vn']['user'] && $upload_acc['fshare_vn']['pass']) {
    $_REQUEST['up_login'] = $upload_acc['fshare_vn']['user'];
    $_REQUEST['up_pass'] = $upload_acc['fshare_vn']['pass'];
    $_REQUEST['action'] = "FORM";
    echo "<b><center>Using Default Login and Pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up = true;
else {
?>
    <table border='0' style="width:270px;" cellspacing='0' align='center'>
        <form method='post'>
            <input type='hidden' name='action' value='FORM' />
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' />&nbsp;</tr>
            <tr><td colspan='2' align='center'><input type='submit' value='Upload' /></tr>
            <tr><td colspan='2' align='center'><small>*You can set it as default in <b><?php echo $page_upload["fshare.vn_member"]; ?></b></small></tr>
    </table>
    </form>

<?php
}

if ($continue_up) {
    $not_done = false;
?>
    <table width='600' align='center'>
    </td></tr>
    <tr><td align='center'>
            <div id='login' width='100%' align='center'>Login to Putlocker</div>
        <?php
        if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
            $post = array();
            $post['login_useremail'] = $_REQUEST['up_login'];
            $post['login_password'] = $_REQUEST['up_pass'];
            $post['url_refe'] = "http%3A%2F%2Fwww.fshare.vn%2Findex.php";
            $Url = parse_url("http://www.fshare.vn/login.php");
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://www.fshare.vn/index.php", 0, $post, 0, $_GET["proxy"], $pauth);
            is_page($page);
            $cookie = GetCookies($page);
        } else {
            echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
        }
        ?>
        <script type='text/javascript'>document.getElementById('login').style.display='none';</script>
        <div id='info' width='100%' align='center'>Retrive upload ID</div>
        <?php
        $Url = parse_url("http://upload.fshare.vn/");
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 0, $cookie, 0, 0, $_GET["proxy"], $pauth);
        is_page($page);
        $SESSID = cut_str($page, '"SESSID" : "', '"');
        unset($post);
        $post['Filename'] = $lname;
        $post['SESSID'] = $SESSID;
        $post['folderid'] = "-1";
        $post['desc'] = "Upload from rapidleech - Plugin make by vdhdevil"; //please dont remove this line thanks
        $up_loc = "http://upload.fshare.vn/upload.php";
        ?>
        <script type='text/javascript'>document.getElementById('info').style.display='none';</script>
        <?php
        $url = parse_url($up_loc);
        $upagent = "Shockwave Flash";
        $upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 0, $cookie, $post, $lfile, $lname, "fileupload", "", $_GET["proxy"], $pauth, $upagent);
        ?>
        <script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>
        <?php
        is_page($upfiles);
        if (!preg_match("#http://www.fshare.vn/file/[A-Z0-9]+/#", $upfiles, $dlink)) {
            html_error("Upload failed or Plugin is out of date");
        }
        $download_link = $dlink[0];
    }

//by vdhdevil
        ?>