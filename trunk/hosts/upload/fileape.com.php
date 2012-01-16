<table width=600 align=center> 
</td></tr> 
<tr><td align=center> 
<div id=login width=100% align=center></div> 
<script>document.getElementById('info').style.display='none';</script>
<div id=info width=100% align=center>Retrive upload ID</div> 
<?php
			$url=parse_url('http://fileape.com/');
			$page = geturl($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://fileape.com/", 0, 0, 0, $_GET["proxy"], $pauth);
			if(!preg_match('#form[\r|\n|\s]+action="([^"]+)"#', $page, $act)){
				html_error('Cannot get form action.', 0);
			}
			$url = parse_url($act[1]);
			$post["raw"]= '0';
			$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), "http://fileape.com/", 0, $post, $lfile, $lname, "file");
			is_page($upfiles);
?>
<script>document.getElementById('progressblock').style.display='none';</script>
<?php
			/*This is a countdown timer, use it persists error: line 91.
			is_page($upfiles);
            insert_timer( 10, "Wait for Redirect Download Link.","",true );
			*/
			preg_match("#[\r|\n|\s]+Location:[\r|\n|\s]+([^']+)#", $upfiles, $dlink);
			$link = split ('[=]', $dlink[1]);
			if ($link[2] != 2){
				if(!empty($link[3])){
					$download_link = 'http://fileape.com/dl/'.$link[3];
				}else{
					html_error ("Didn't find download link!");
				}
			}else{
				html_error ("The file you submitted was too small or submitted improperly. Please try again.");
			}
/**
written by simplesdescarga 14/01/2012
**/   
?>