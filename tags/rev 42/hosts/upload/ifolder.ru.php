<?php
 function Encode ( $str, $type )
{
 // $type: 
// 'w' - encodes from UTF to win 
 // 'u' - encodes from win to UTF 

    static $conv='';
    if (!is_array ( $conv ))
    {    
        $conv=array ();
        for ( $x=128; $x <=143; $x++ )
        {
          $conv['utf'][]=chr(209).chr($x);
          $conv['win'][]=chr($x+112);
        }

        for ( $x=144; $x <=191; $x++ )
        {
               $conv['utf'][]=chr(208).chr($x);
               $conv['win'][]=chr($x+48);
        }
 
        $conv['utf'][]=chr(208).chr(129);
        $conv['win'][]=chr(168);
        $conv['utf'][]=chr(209).chr(145);
        $conv['win'][]=chr(184);
     }
     if ( $type=='w' )
          return str_replace ( $conv['utf'], $conv['win'], $str );
     elseif ( $type=='u' )
          return str_replace ( $conv['win'], $conv['utf'], $str );
     else
        return $str;
  }

if (isset($_POST['action']))
	{
		$post[$_POST['desc_id']]=$descript;
		$post[$_POST['pass_id']]="";
		$post['confirmed_number']=$_POST['confirmed_number'];
		$post['email'] = "";
		$post['session']=$_POST['session_id'];
		$post['action']="Подтвердить";
		$url = parse_url($_POST['url']);
		$page = geturl($url['host'], 80, $url['path']."?".$url["query"], "http://ifolder.ru/", 0,$post, 0, "");
		is_page($page);
		if (preg_match('/confirmed_number/',$page)) html_error("Error code, retry again!");
		preg_match('%ifolder\\.ru/\\d+%',$page,$down);
		if (empty($down[0])) html_error("error get download link");
		$download_link = "http://".$down[0];
		$adm_link = "http://ifolder.ru/control/".cut_str($page,'"http://ifolder.ru/control/','"');
	}
		else
	{            
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=info width=100% align=center>Retrive upload ID</div>
<?php
		$page = geturl("ifolder.ru", 80, "/", "", 0, 0, 0, ""); 
		$action_url = cut_str($page,'form-data" action="','"');
		$url = parse_url($action_url);
		$post['upload_params']=cut_str($page,'params" value="','"');
		$post['clone']=cut_str($page,'clone" value="','"');
		$post['progress_bar']=cut_str($page,'_bar" value="','"');
		$post['upload_host']=cut_str($page,'host" value="','"');
		$post['MAX_FILE_SIZE']=104857600;
		$post['show_progress_bar']=0;
?>
<script>document.getElementById('info').style.display='none';</script>
    
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

            $upfiles=upfile($url['host'],80,$url['path']."?".$url["query"],"http://ifolder.ru/", 0, $post, $lfile, $lname, "filename");
            is_page($upfiles);

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php          
            $tmp_url = trim(cut_str($upfiles,"Location:","\n"));
            if (!$tmp_url){
                html_error("Error Upload!!!");
            }
            unset($url);
            $url = parse_url($tmp_url);
            $page = geturl($url['host'], 80, $url['path']."?".$url["query"], "http://ifolder.ru/", 0, 0, 0, "");
            is_page($page);
            if (preg_match('/sys_msg/',$page))
				{
				$error=cut_str($page,'sys_msg>','<');
				$error=Encode($error,"w");
				html_error("Error Upload, vozmozhno na vash ip ban!<br>".$error);
				}
            //print_r($page);
            $desc_id = "descr_".cut_str($page,"descr_",'"');
            $pass_id = "password_".cut_str($page,"password_",' ');
            $img_link = "/random/images/".cut_str($page,"/random/images/",'"');
            $session_id = cut_str($page,'session" value=','>');   

			$page=Encode($page,"w");
			
            
?>          
<form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER ['QUERY_STRING']?>" method="POST">
    <input type="hidden" name="desc_id" value=<?php echo $desc_id?>>
    <input type="hidden" name="pass_id" value=<?php echo $pass_id?>>
    <input type="hidden" name="session_id" value=<?php echo $session_id?>>
    <input type="hidden" name="url" value=<?php echo $tmp_url?>>
    <input type="hidden" name="filename" value=<?php echo base64_encode($_REQUEST['filename']); ?>>
    Please, enter the following figures specified on a picture:<br> <?php echo "(<span id='capt'>не удалось найти цифры, попробуйте ввести все</span>)<br>" ?> <img src="http://ifolder.ru<?php echo $img_link?>"> to here <input type="text" class="text" name="confirmed_number">
    <input type="submit" name="action" value="Get Link's"  style="width:80px;">
</form>
</td></tr></table>
<?php      
			if(($numonly=cut_str($page, "var capt = document.getElementById('capt');", "</script>"))) echo "<script>\nvar capt = document.getElementById('capt');\n$numonly\n</script>";
			elseif(($numonly=cut_str($page, "<font color=Red>", "</font>"))!="<b><div id=\"capt\"></div></b>") echo "<script>document.getElementById('capt').innerHTML=\"$numonly\"</script>";
			die();
		}

// Edited by sert 23.06.2008
?>