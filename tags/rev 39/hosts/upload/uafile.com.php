<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php
      
      $post['name']=$lname;
      $post['submit']="Upload!";
      
      $upfiles=upfile("www.uafile.com",80, "/upload.php" ,"http://uafile.com/index.php", 0, $post, $lfile, $lname, "upfile");
   
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
		$tmp = cut_str($upfiles,'</center> <p><center> <a href="','"');
		$tmp2 =  cut_str($upfiles,'del=','"');
		if (empty($tmp)){
			print_r($upfiles);
			html_error("Error retrive download link!");
		}
	     $download_link=$tmp;
	     $delete_link = $tmp.'&amp;del='.$tmp2;
?>