<?php

if($_POST["upc"]=="ok"){
}else{
//********* Login ************
$_POST["login"]="";// username
$_POST["pwd"]="";  // password
//****************************
}
if(!$_POST["login"] || !$_POST["pwd"])
{
?>
      <table border=1 style="width: 540px;" cellspacing=0 align=center>
    <form method=post> 
    
    <tr >
      <td colspan=4 align=center height=25px ><b>Enter Premium Account</b> </td>
    </tr>
    <tr>
        <td nowrap>&nbsp;Login        
        <td>&nbsp;<input name=login value='' style="width: 160px;" />&nbsp;        
        <td nowrap>&nbsp;Password        
        <td>&nbsp;<input type=password name=pwd value='' style="width: 160px;" />&nbsp;    
    </tr>    
            <input type=hidden  name=upc value='ok' />
    <tr><td colspan=4 align=center><input type=submit value='Upload' /></tr>    
</table>
</form>
    
<?php
}
else
{

    echo "<div id=login width=100% align=center>Login to uploading.com</div>";
   
    $post=array();
    $post["email"]=$_POST["login"]; 
    $post["password"]=$_POST["pwd"];
     
    $page = geturl("www.uploading.com", 80, "/general/login_form/", 0, 0, $post, 0 );
    $cookies=GetCookies($page);
    $page = geturl("www.uploading.com", 80, "/", "http://www.uploading.com/login", $cookies);
    is_notpresent($page, "Membership: ", "Login failed<br>Wrong login/password?");
    
    echo
<<<HTML
    <script>document.getElementById('login').style.display='none';</script>
    <table width=600 align=center>
    </td></tr>
    <tr><td align=center>
HTML;

    preg_match('/http:\/\/.+uploading\.com\/upload_file\/[^"\']+/', $page, $preg) or html_error("Upload url not found");
    $id = "";
    for($i=0; $i<32; $i++)
    {
        $id .= base_convert(floor(rand(0, 15)), 10, 16);
    }

    
    $url = parse_url($preg[0] ."?X-Progress-ID=". $id);
    
    $post = array
        (
            progress_id    => $id,
            description => "",
            share_email    => "",
            pass => ""
        );

    $page = upfile($url["host"], defport($url), $url["path"].($url["query"] ? "?".$url["query"] : ""), "http://www.uploading.com/", $cookies, $post, $lfile, $lname, "file");
    
    echo
<<<HTML
    <script>document.getElementById('progressblock').style.display='none';</script>
HTML;

    $ddl=cut_str($page,'parent.location="','"');
    $Url=parse_url($ddl);

    $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"], $Referer, $cookies, $post, 0, $_GET["proxy"],$pauth);
    is_page($page);

    preg_match('/http:\/\/uploading\.com\/files\/\w{7,9}\/[^\'"]+/i', $page, $preg) or html_error("Upload error");
    preg_match('/http:\/\/uploading\.com\/files\/edit\/[^\'"]+/i', $page, $pregd);
    $download_link = $preg[0];
    $delete_link = $pregd[0];
    echo "<h3><font color='green'>File successfully uploaded to your account</font></h3>";    
}

/*************************\  
WRITTEN by kaox 07/05/09
UPDATE  by kaox 05/09/09
\*************************/


?>
