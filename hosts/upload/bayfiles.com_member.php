<?php
####### Account Info. ###########
$upload_acc['bayfiles_com']['user'] = ""; //Set you username
$upload_acc['bayfiles_com']['pass'] = ""; //Set your password
##############################

$not_done = true;
$continue_up = false;
if ($upload_acc['bayfiles_com']['user'] && $upload_acc['bayfiles_com']['pass']) {
    $_REQUEST['up_login'] = $upload_acc['bayfiles_com']['user'];
    $_REQUEST['up_pass'] = $upload_acc['bayfiles_com']['pass'];
    $_REQUEST['action'] = "FORM";
    echo "<b><center>Using Default Login and Pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up = true;
else {
    ?>
    <form method='post' autocomplete="off">
        <input type='hidden' name='action' value='FORM' />
        <table style="text-align: center;width:270px;border-style: none;border-collapse: collapse">
            <tr><td nowrap>&nbsp;User*<td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' />&nbsp;</tr>
            <tr><td colspan='2' align='center'><input type='submit' value='Upload' /></tr>
            <tr><td colspan='2' align='center'><small>*You can set it as default in <b><?php echo $page_upload["bayfiles.com_member"]; ?></b></small></tr>
        </table>    
    </form>

    <?php
}

if ($continue_up) {
    $not_done = false;
    ?>
    <table width='600' align='center'>        
        <tr><td align='center'>
                <div id='login' width='100%' align='center'>Login to Bayfiles</div>
                <?php
                if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
                    $post = array();
                    $post['action']="login";
                    $post['username']=$_REQUEST['up_login'];
                    $post['password']=$_REQUEST['up_pass'];
                    $post['next']="%252F";
                    $post['']='';                    
                    $Url = parse_url("http://bayfiles.com/ajax_login");
                    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://bayfiles.com/\r\nX-Requested-With: XMLHttpRequest", 0, $post, 0, $_GET["proxy"], $pauth);
                    is_page($page);
                    $cookie = GetCookies($page);
                    is_notpresent($page, '{"reload":true}', "Login Failed");
                } else {
                    echo "<b><center>Login not found or empty, using non member upload.</center></b>\n";
                }
                ?>
                <script type='text/javascript'>document.getElementById('login').style.display='none';</script>
                <div id='info' width='100%' align='center'>Retrive upload ID</div>
                <?php
                $Url = parse_url("http://bayfiles.com/ajax_upload?_=".  microtime(true));
                $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), "http://bayfiles.com/\r\nX-Requested-With: XMLHttpRequest", $cookie, 0, 0, $_GET["proxy"], $pauth);
                is_page($page);
                $upload_url= str_replace("\\","",cut_str($page, '"upload_url":"', '"'));
                $url=  parse_url($upload_url);
                ?>
                <script type='text/javascript'>document.getElementById('info').style.display='none';</script>
                <?php
                $upagent = "Mozilla/5.0 (Windows NT 5.1; rv:6.0) Gecko/20100101 Firefox/6.0";
                $upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://bayfiles.com/", $cookie, 0, $lfile, $lname, "file", "", $_GET["proxy"], $pauth, $upagent);
                ?>
                <script type='text/javascript'>document.getElementById('progressblock').style.display='none';</script>
                <?php
                is_page($upfiles);
                $download_link= str_replace("\\","",cut_str($upfiles, '"downloadUrl":"', '"'));
                $delete_link=str_replace("\\","",cut_str($upfiles, '"deleteUrl":"', '"'));                
            }

//by vdhdevil
            ?>