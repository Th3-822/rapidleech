<?php
error_reporting(E_ALL);
####### Free Account Info. ###########
$uploaded_username = ""; //  Set you username
$uploaded_password = ""; //  Set your password
##############################

$not_done = true;
$continue_up = false;
if ($uploaded_username & $uploaded_password)
{
    $_REQUEST['my_login'] = $uploaded_username;
    $_REQUEST['my_pass'] = $uploaded_password;
    $_REQUEST['action'] = "FORM";
    echo "<b><center>Use Default login/pass.</center></b>\n";
}
if ($_REQUEST['action'] == "FORM")
    $continue_up = true;
else
{
?>
    <table border=0 style="width:270px;" cellspacing=0 align=center>
        <form method=post>
            <input type=hidden name=action value='FORM' />
            <tr><td nowrap>&nbsp;Username*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
            <tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
            <tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
            <tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["uploaded.to"]; ?></b></small></tr>
    </table>
    </form>

<?php
}

if ($continue_up)
{
    $not_done = false;
?>
    <table width=600 align=center>
    </td></tr>
    <tr><td align=center>
            <div id=info width=100% align=center>Retrive upload ID</div>
        <?php
        $usr = $_REQUEST['my_login'];
        $pass = $_REQUEST['my_pass'];
        $referrer = "http://uploaded.to/";
        $Url = parse_url('http://uploaded.to/io/login');
        $post['id'] = $usr;
        $post['pw'] = $pass;
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $referrer, 0, $post, 0, $_GET["proxy"], $pauth);
        is_page($page);
        $cookie = GetCookies($page) . ';lang=en';

        $Url = parse_url("http://uploaded.to/home");
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"], $pauth);
        is_page($page);
        is_notpresent($page, "Logout", "Not logged in. Check your login details in uploaded.to.php");
        //Get url of the upload server
        $Url = parse_url("http://uploaded.to/js/script.js");
        $script = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"], $pauth);
        //Generate edit code
        $editKey = generate(6);
        $serverUrl = cut_str($script, 'uploadServer = \'', '\'') . 'upload?admincode=' . $editKey . '&id=' . $usr . '&pw=' . sha1($pass);
        //Precheck with file info, editKey and a random fileID (to avoid collision when uploading simultaneus file)
        $Url = parse_url("http://uploaded.to/io/upload/precheck");
        $id = mt_rand(0, 15);
        $fileInfo['id'] = 'file' . $id;
        $fileInfo['editKey'] = $editKey;
        $fileInfo['size'] = filesize($lfile);
        $fileInfo['name'] = $lname;
        geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 'http://uploaded.to/upload', $cookie, $fileInfo, 0, $_GET["proxy"], $pauth);
        //Duplicity check with same info as precheck
        $Url = parse_url("http://uploaded.to/io/upload/duplicity");
        geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), 'http://uploaded.to/upload', $cookie, $fileInfo, 0, $_GET["proxy"], $pauth);
        ?>
        <script>document.getElementById('info').style.display='none';</script>

        <table width=600 align=center>
    </td>
</tr>
<tr>
    <td align=center>
        <?php
        $url = parse_url($serverUrl);
        $upagent = "Shockwave Flash";
        $fpost['Filename'] = $lname;
        $fpost['Upload'] = 'Submit Query';
        $upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), '', 'lang=en', $fpost, $lfile, $lname, "Filedata");
        ?>
        <script>document.getElementById('progressblock').style.display='none';</script>
        <?php
        insert_timer(10, 'Wait for the host to update file list.');
        //Get the link from the list of all uploaded file.
        /* $Url = parse_url("http://uploaded.to/me/files/list");
          $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $referrer, $cookie, 0, 0, $_GET["proxy"], $pauth);
          is_page($page);
          preg_match('#http://(.+)'.str_replace(array('[',']','.'), array('\[','\]','\.'), $lname).'#', $page,$links);
          $download_link=$links[0]; */
        $download_link = getDlLink($cookie, $lname);
        //print_r($links);
        if (empty($download_link))
        {
            insert_timer(5, 'Try Again to read the file list');
            $download_link = getDlLink($cookie, $lname);
        }
        if (empty($download_link))
            html_error('There was a problem with the upload server (maybe overloaded). Please try later.');
        $adm_link = $editKey;
    }

    function generate($len) {
        $pwd = '';
        $con = Array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z');
        $voc = Array('a', 'e', 'i', 'o', 'u');

        for ($i = 0; $i < $len / 2; $i++)
        {
            $c = mt_rand(0, 19);
            $v = mt_rand(0, 4);
            $pwd .= $con[$c] . $voc[$v];
        }

        return $pwd;
    }

    function getDlLink($cookie, $lname) {
        $referrer = "http://uploaded.to/me";
        $Url = parse_url("http://uploaded.to/io/me/list/files");
        $search['dir'] = 'desc';
        $search['page'] = 0;
        $search['limit'] = 11;
        $search['order'] = 'date';
        $search['search'] = $lname;
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"] . ($Url["query"] ? "?" . $Url["query"] : ""), $referrer, $cookie, $search, 0, $_GET["proxy"], $pauth);
        is_page($page);
        $id = substr($page, strpos($page, '"id":'),strpos($page,',"date"')-strpos($page, '"id":'));
        $id=substr($id,6,-1);
        if(strpos($page, '"id":')!==false)
            return 'http://ul.to/' . $id;
        else
            return null;
    }

    /**     * **********************\
      written by kaox 14/06/2009
     * edited by Balor 15/03/2010 (new Layout with YUI uploader)
      \************************ */
        ?>