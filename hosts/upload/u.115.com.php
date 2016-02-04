<?php

####### Account Info. ###########
$u_115_login = ""; //Set you username
$u_115_pass = ""; //Set your password
##############################

$not_done=true;
$continue_up=false;
if ($u_115_login & $u_115_pass)
    {
        $_REQUEST['my_login'] = $u_115_login;
        $_REQUEST['my_pass'] = $u_115_pass;
        $_REQUEST['action'] = "FORM";
        echo "<b><center>Use Default login/pass.</center></b>\n";
    }

if ($_REQUEST['action'] == "FORM")
    $continue_up=true;
else{
?>
<table border=0 style="width:270px;" cellspacing=0 align=center>
<form method=post>
<input type=hidden name=action value='FORM' />
<tr><td nowrap>&nbsp;User*<td>&nbsp;<input type=text name=my_login value='' style="width:160px;" />&nbsp;</tr>
<tr><td nowrap>&nbsp;Password*<td>&nbsp;<input type=password name=my_pass value='' style="width:160px;" />&nbsp;</tr>
<tr><td colspan=2 align=center><input type=submit value='Upload' /></tr>
<tr><td colspan=2 align=center><small>*You can set it as default in <b><?php echo $page_upload["u.115.com"]; ?></b></small></tr>
</table>
</form>
<?php
    }

if ($continue_up)
    {
        $not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id="login" width=100% align=center>Login to U.115.com</div>
<?php 
        $post['login[account]'] = $_REQUEST['my_login'];
        $post['login[passwd]'] = $_REQUEST['my_pass'];
        $post['back'] = "http://www.115.com";
        $post['goto'] = "http%3A%2F%2F115.com";

        $page = geturl("passport.115.com", 80, "/?action=login", 0, 0, $post);            
        is_page($page);
        $cookie1 = GetCookies($page);
        
      //  echo (" <div> Page : $page </div>");
        
        $errorcode =cut_str ($page , "name='error_code' value='","'");
      //  echo (" <div> errorcode : $errorcode </div>");
      
        if($errorcode)
        {
             html_error("Error logging in - are your logins correct!");
        } 
      
      //  $linkaction =cut_str ($page ,' Location: ','');
      //  echo (" <div> linkaction : $linkaction </div>");
      //  
      //  if(!$linkaction)
      //  {
      //       html_error("Error logging in - are your logins correct!");
      //  } 
      //  $Url = parse_url($linkaction);
      //  $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), 0, $cookie1, 0, 0, $_GET["proxy"],$pauth);    
      //  is_page($page);
      //  $cookie = GetCookies($page);
?>
<script>document.getElementById('login').style.display='none';</script>
<div id="info" width=100% align="center">Retrive upload ID</div>

<?php 
        $Url = parse_url("http://115.com/");
        $page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""),"http://www.115.com/", $cookie1, 0, 0, $_GET["proxy"],$pauth);    
        
        $cookie = GetCookies($page);
        
        $Upload_url=cut_str ($page ,'"aid":1,"upload_url":"','",');
        $Upload_url=str_replace('\/','/',$Upload_url);
        
       // echo (" <div> Upload_url : $Upload_url </div>");
        
        $cookie_up =cut_str ($page ,"var USER_COOKIE = '","';");
        
        if(!$Upload_url)
        {
            html_error($Upload_url);
        }
?>
<script>document.getElementById('info').style.display='none';</script>
<?php 
        $url = parse_url ($Upload_url); 
        //$url= parse_url("http://up.u.115.com/upload");
        $fpost = array();
        $fpost['Filename'] = $lname;
        $fpost['cookie'] = $cookie_up;
        $fpost['aid'] = '1';
        $agent='Shockwave Flash';
        $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://u.115.com/?ct=index&ac=my", $cookie, $fpost, $lfile, $lname, "Filedata" );
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
        //,"pick_code":"e65lenbo","ico":"
        $pickcode =cut_str ($upfiles ,'"pick_code":"','","ico"');
        //echo (" <div> pickcode : $pickcode </div>");
        //echo (" <div> upfiles : $upfiles  </div>");
        if(!$pickcode)
        {
             html_error("Finished, Go to your account to see Download-URL.");
        }
        else
        {
            echo (" <div> Note: You required login before you can see it. </div>");
            $download_link = "http://u.115.com/file/$pickcode";
        }
        // echo (" <div> Mark </div>");
    }
?>