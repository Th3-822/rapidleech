<?php
####### Account Info. ###########
$upload_acc['jumbofile_com']['user'] = ""; //Set you username
$upload_acc['jumbofile_com']['pass'] = ""; //Set your password
$upload_acc['jumbofile_com']['linkpass']="";//blank for non password
##############################

$not_done = true;
$continue_up = false;
if ($upload_acc['jumbofile_com']['user'] && $upload_acc['jumbofile_com']['pass']) {
    $_REQUEST['up_login'] = $upload_acc['jumbofile_com']['user'];
    $_REQUEST['up_pass'] = $upload_acc['jumbofile_com']['pass'];
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
            <tr><td colspan='2' align='center'><small>*You can set it as default in <b><?php echo $page_upload["jumbofile.com_member"]; ?></b></small></tr>
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
            <div id='login' width='100%' align='center'>Login to Jumbofile</div>
        <?php
        if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
            $post = array();
            $post['op'] = "login";
            $post['redirect'] = "";
            $post['login'] = $_REQUEST['up_login'];
            $post['password'] = $_REQUEST['up_pass'];
            $post['x'] = rand(0, 33);
            $post['y'] = rand(0, 6);
            $Url = parse_url("http://jumbofiles.com/");
            $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://jumbofiles.com/login.html", array("popundr"=>"1"), $post, 0, $_GET["proxy"], $pauth);
            is_page($page);
            $cookie = GetCookies($page);
            is_notpresent($cookie, "fss=", "Login Failed");
        } else {
            echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
        }
        ?>
        <script type='text/javascript'>document.getElementById('login').style.display='none';</script>
        <div id='info' width='100%' align='center'>Retrive upload ID</div>
        <?php
        
        $Url = parse_url("http://jumbofiles.com/");
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://jumbofiles.com/?op=my_files", $cookie, 0, 0, $_GET["proxy"], $pauth);
        is_page($page);
        unset($post);
        $post['upload_type'] = "file";
        $post['sess_id'] = cut_str($page, 'sess_id" value="', '"');
        $post['srv_tmp_url'] = cut_str($page, 'srv_tmp_url" value="', '"');
        $uid = "";
        for ($i = 0; $i < 12; $i++) {
            $uid = $uid . rand(0, 9);
        }
        $uploc = cut_str($page, 'multipart/form-data" action="', '"') . $uid . '&js_on=1&utype=reg&upload_type=file';
        $url = parse_url($uploc);
        $post['link_rcpt']="";
        $post['link_pass']=$upload_acc['jumbofile_com']['linkpass'];
        $post['tos']="1";
        $post['x']=rand(0,61);
        $post['y']=rand(0,17);
        ?>
        <script type='text/javascript'>document.getElementById('info').style.display='none';</script>
        <?php
        $upagent = "Mozilla/5.0 (Windows NT 5.1; rv:6.0) Gecko/20100101 Firefox/6.0";
        $upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 0, $cookie, $post, $lfile, $lname, "file_0", "", $_GET["proxy"], $pauth, $upagent);
        ?>
        <script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>
        <?php
        is_page($upfiles);
        $locat = cut_str($upfiles, 'rea name=\'fn\'>', '</textarea>');
        unset($post);
        $gpost['fn'] = $locat;
        $gpost['st'] = "OK";
        $gpost['op'] = "upload_result";
        $Url = parse_url("http://jumbofiles.com/");
        $page = geturl($Url["host"], defport($Url), $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $uploc, $cookie, $gpost, 0, $_GET["proxy"], $pauth);
        $download_link = "http://jumbofiles.com/".$locat;
        preg_match('#http://jumbofiles.com/'.$locat.'\?killcode[^<"]+#', $page, $tmp);
        $delete_link = $tmp[0];
    }

//by vdhdevil
        ?>