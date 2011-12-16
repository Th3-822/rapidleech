<?php
####### Account Info. ###########
$upload_acc['megaupload_com']['user'] = ""; //Set you username
$upload_acc['megaupload_com']['pass'] = ""; //Set your password
##############################

function generateRandomID($param) {
    $tmp = "0" . (microtime(true) * 100);
    $len = $param - strlen($tmp);
    $count = 1;
    while ($count <= $len) {
        $tmp .= rand(0, 9);
        $count++;
    }
    return $tmp;
}

$not_done = true;
$continue_up = false;
if ($upload_acc['megaupload_com']['user'] && $upload_acc['megaupload_com']['pass']) {
    $_REQUEST['up_login'] = $upload_acc['megaupload_com']['user'];
    $_REQUEST['up_pass'] = $upload_acc['megaupload_com']['pass'];
    $_REQUEST['action'] = "FORM";
    echo "<b><center>Using Default Login and Pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up = true;
else {
    ?>
    <form method='post'>
        <input type='hidden' name='action' value='FORM' />
        <table style="text-align: center;width:270px;border-style: none;border-collapse: collapse;margin-left: auto;margin-right: auto">
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' />&nbsp;</tr>
            <tr><td colspan='2' align='center'><input type='submit' value='Upload' /></tr>
            <tr><td colspan='2' align='center'><small>*You can set it as default in <b><?php echo $page_upload["megaupload.com_member"]; ?></b></small></tr>
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
                <div id='login' width='100%' align='center'>Login to Megaupload</div>
                <?php
                if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
                    $post = array();
                    $post['login'] = "1";
                    $post['redir'] = "1";
                    $post['username'] = $_REQUEST['up_login'];
                    $post['password'] = $_REQUEST['up_pass'];
                    $Url = parse_url("http://megaupload.com/?c=login");
                    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://megaupload.com/?c=login", 0, $post, 0, $_GET["proxy"], $pauth);
                    is_page($page);
                    $Cookies = GetCookies($page);                    
                    if (!strpos($Cookies, "ser=")) {
                        html_error("Login Failed");
                    }
                } else {
                    echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
                }
                ?>
                <script type='text/javascript'>document.getElementById('login').style.display='none';</script>
                <div id='info' width='100%' align='center'>Retrive upload ID</div>
                <?php
                $Url = parse_url("http://www.megaupload.com/?login=1");
                $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://megaupload.com/?c=login", $Cookies, 0, 0, $_GET["proxy"], $pauth);
                is_page($page);
                if (!preg_match_all("#http://www\d+.megaupload.com/#", $page, $tmp)) {
                    html_error("Upload server not found");
                }
                $uploadserver=$tmp[0][rand(0, count($tmp[0])-1)];                
                $UPLOAD_IDENTIFIER = generateRandomID(32);
                preg_match("#user=([\w-\.]+)#", $Cookies, $user);                
                $upload_url = $uploadserver."upload_done.php?UPLOAD_IDENTIFIER=$UPLOAD_IDENTIFIER&user=$user[1]&s=" . filesize($lfile);
                $url=parse_url($upload_url);
                $dpost=array();
                $dpost["Filename"]=$lname;
                $dpost["message"]="Upload by rapidleech";
                $dpost['password']="";
                $dpost['user']=$user[1];
                ?>
                <script type='text/javascript'>document.getElementById('info').style.display='none';</script>
                <?php
                $upagent = "Shockwave Flash";
                $upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 0, $Cookies, $dpost, $lfile, $lname, "Filedata", "", $_GET["proxy"], $pauth, $upagent);
                ?>
                <script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>
                <?php
                is_page($upfiles);
                if (!preg_match("#http://www.megaupload.com/\?d=\w+#", $upfiles, $done)){
                    html_error("Upload link not found");
                }
                $download_link = $done[0];                
            }

//by vdhdevil
            ?>