<table width=600 align=center>
</td></tr>
<tr><td align=center>
<?php

# author tester2006 30.10.08

    $page=geturl("filex.kz", 80, "/");
    $post["apply"]=1;
    $post["UPLOAD_IDENTIFIER"]="";
    
    $upfiles=upfile("filex.kz", 80, "/upload.php", "http://filex.kz/", 0, $post, $lfile, $lname, "upload_file");
    echo "<script>document.getElementById('progressblock').style.display='none';</script>";
    
    $tmp = cut_str($upfiles,'id=', "'"); 
    $download_link = "http://filex.kz/?action=download&id=".$tmp;
    if(!$download_link) html_error("Error uploading or get direct link"); 
?>
    