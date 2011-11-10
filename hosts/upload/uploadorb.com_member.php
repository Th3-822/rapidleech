<?php
####### Account Info. ###########
$upload_acc['uploadorb_com']['user'] = ""; //Set you username
$upload_acc['uploadorb_com']['pass'] = ""; //Set your password
##############################

$not_done = true;
$continue_up = false;
if ($upload_acc['uploadorb_com']['user'] && $upload_acc['uploadorb_com']['pass']) {
    $_REQUEST['up_login'] = $upload_acc['uploadorb_com']['user'];
    $_REQUEST['up_pass'] = $upload_acc['uploadorb_com']['pass'];
    $_REQUEST['action'] = "FORM";
    echo "<b><center>Using Default Login and Pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up = true;
else {
    ?>
    <form method='post'>
        <input type='hidden' name='action' value='FORM' />
        <table style="text-align: center;width:270px;border-style: none;border-collapse: collapse">
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' />&nbsp;</tr>
            <tr><td colspan='2' align='center'><input type='submit' value='Upload' /></tr>
            <tr><td colspan='2' align='center'><small>*You can set it as default in <b><?php echo $page_upload["uploadorb.com_member"]; ?></b></small></tr>
        </table>    
    </form>

    <?php
}

if ($continue_up) {
    $not_done = false;
    ?>
    <table width='600' align='center'>
        <tr><td></td></tr>
        <tr><td align='center'>
                <div id='login' width='100%' align='center'>Login to Uploadorb</div>

                <?php
                if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
                    $post = array();                    
                    $post['op'] = "login";
                    $post['redirect'] = "http%3A%2F%2Fuploadorb.com%2F";
                    $post['login'] = $_REQUEST['up_login'];
                    $post['password'] = $_REQUEST['up_pass'];
                    $post['x'] = rand(0, 41);
                    $post['y'] = rand(0,8);
                    $Url = parse_url("http://uploadorb.com/");
                    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://uploadorb.com/login.html", 0, $post, 0, $_GET["proxy"], $pauth);
                    is_page($page);
                    preg_match_all('/Set-Cookie: (.*);/U', $page, $temp);
                    $cookie = $temp[1];
                    $cookies = implode('; ', $cookie);                    
                    if (!preg_match("#xfss=([a-z0-9]+)#", $cookies, $tmp)){
                        html_error("Login Failed");
                    }
                    $xfss = $tmp[1];                    
                } else {
                    echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
                }
                ?>
                <script type='text/javascript'>document.getElementById('login').style.display='none';</script>
                <div id='info' width='100%' align='center'>Retrive upload ID</div>
                <?php
                $Url = parse_url("http://uploadorb.com/");
                $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://uploadorb.com/login.html", $cookies, 0, 0, $_GET["proxy"], $pauth);
                is_page($page);
                $uid = "";
                for ($i = 0; $i < 12; $i++) {
                    $uid.=rand(0, 9);
                }
                unset($post);
                $post['upload_type'] = "file";
                $post['sess_id'] = $xfss;                
                $post['srv_tmp_url'] = cut_str($page, 'name="srv_tmp_url" value="', '"');
                $post['file_0_descr']="Upload by rapidleech - @vdhdevil";//set description here
                $post['link_rcpt']="";
                $post['link_pass']="";//set password link here
                $post['tos']=1;
                $post['submit_btn']=" Upload!";
                $action_url = cut_str($page, 'multipart/form-data" action="', '"');
                
                $action_url=$action_url.$uid."&js_on=1&utype=reg&upload_type=file";
                $url = parse_url($action_url);
                ?>
                <script type='text/javascript'>document.getElementById('info').style.display='none';</script>
                <?php
                $upagent = "Mozilla/5.0 (Windows NT 5.1; rv:6.0) Gecko/20100101 Firefox/6.0";
                $upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://uploadorb.com/", $cookies, $post, $lfile, $lname, "file_0", "file_1", $_GET["proxy"], $pauth, $upagent);
                ?>
                <script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>
                <?php
                is_page($upfiles);
                $locat = cut_str($upfiles, "<textarea name='fn'>", "</t");
                $gpost = array();
                $gpost['op'] = "upload_result";
                $gpost['fn'] = $locat;
                $gpost['st'] = "OK";
                $Url = parse_url("http://uploadorb.com/");
                $page = geturl($Url["host"], 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $action_url, $cookies, $gpost);
                is_page($page);
                $ddl = cut_str($page, 'copy(this);"><a href="', '"');
                $del = cut_str($page, 'killcode=', '</');
                $download_link = $ddl;
                $delete_link = $ddl . '?killcode=' . $del;
            }

//by vdhdevil
            ?>