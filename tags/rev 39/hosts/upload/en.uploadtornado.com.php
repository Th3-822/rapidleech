<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

      $post['name']=$lname;
      $post['tos']="1";
      $post['submit']="Upload!";
      
      $upfiles=upfile("www.en.uploadtornado.com",80, "/cgi-bin/upload.cgi?upload_id=" ,"www.en.uploadtornado.com", 0, $post, $lfile, $lname, "upfile");
   
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			$location = parse_url(trim(cut_str($upfiles,"location: ","\n")));
			$page = geturl($location['host'], 80, $location['path']."?".$location["query"], "http://uploadtornado.com", 0, 0, 0, "");
			is_page($page);
			is_notpresent($page,'Download Link','Error Get Download Link!!!');
			$tmp = cut_str($page,"Download Link","Link for forums:");
			$download_link = "http://en.uploadtornado.com".cut_str($tmp,"http://en.uploadtornado.com",'"');

?>

