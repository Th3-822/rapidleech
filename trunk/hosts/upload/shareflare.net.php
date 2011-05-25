<?php
$continue_up=true;
if ($continue_up)
	{
		$not_done=false;
?>
<table width=600 align=center>
</td></tr>
<tr><td align=center>
<div id=login width=100% align=center>Login to Shareflare.net</div>
<?php 
			$page = geturl("shareflare.net", 80, "/", 0, 0, 0, 0, $_GET["proxy"], $pauth);
			is_page($page);
                        preg_match("%var ACUPL_UPLOAD_SERVER = '(.*)';%i", $page, $acup);
                        $ACUPL_UPLOAD_SERVER = $acup[1];
                        $MAX_FILE_SIZE = cut_str($page, '<input type="hidden" name="MAX_FILE_SIZE" value="','">');		
	?>
<script>document.getElementById('login').style.display='none';</script>
<div id=info width=100% align=center>Retrive test</div>
<script>document.getElementById('info').style.display='none';</script>
<?php 
                        $url_up = $ACUPL_UPLOAD_SERVER;
                        $acupl_UID = GRC_0();
                        $acupl_UID1 = GRC();
                        $fpost = array(
			'MAX_FILE_SIZE' => $MAX_FILE_SIZE,
			'owner'         => '',
			'pin'           => '',
                        'base'          => 'shareflare',
                        'host'          => 'shareflare.net');
			$url=parse_url($url_up.'/marker='.$acupl_UID.'_'.$acupl_UID1.'');
		        $upfiles = upfile($url["host"],$url["port"] ? $url["port"] : 80, $url["path"].($url["query"] ? "?".$url["query"] : ""),"http://shareflare.net/index.php", 0, $fpost, $lfile, $lname, "file0");                        
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php 	
			is_page($upfiles);
                        $Url = parse_url('http://shareflare.net/acupl_proxy.php?srv='.$url_up.'&uid='.$acupl_UID.'_'.$acupl_UID1.'');
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://shareflare.net/index.php", 0, 0, 0, $_GET["proxy"],$pauth);
			preg_match('%post_result": "(.*)", "now_time"%', $page, $result);
	                $link_result = $result[1];
                        $Url = parse_url($link_result);
			$page = geturl($Url["host"], $Url["port"] ? $Url["port"] : 80, $Url["path"].($Url["query"] ? "?".$Url["query"] : ""), "http://shareflare.net/index.php", 0, 0, 0, $_GET["proxy"],$pauth);
                        preg_match("%http://shareflare.net/download/(.*)%",$page, $dllink);
                        if (!$dllink[1]) html_error('Error getting return url');
                        preg_match("%http://shareflare.net/download/delete(.*)%",$page, $delink);
                        $download_link=$dllink[0];
                        $delete_link= $delink[0];                              		
	}
function GRC($length = 40, $letters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz')
  {
      $s = '';
      $lettersLength = strlen($letters)-1;
      for($i = 0 ; $i < $length ; $i++)
      {
      $s .= $letters[rand(0,$lettersLength)];
      }
      return $s;
  } 
function GRC_0($length = 11, $letters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZ')
  {
      $s = '';
      $lettersLength = strlen($letters)-1;
      for($i = 0 ; $i < $length ; $i++)
      {
      $s .= $letters[rand(0,$lettersLength)];
      }
      return $s;
  } 
// written by VinhNhaTrang 09/11/2010
?>