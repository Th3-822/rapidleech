<?php

require_once('rl_init.php');

// Access denied page
include(TEMPLATE_DIR.'header.php');
?>
<div id="wrapper">
	<section id="main" class="wrapper">
	<div class="inner">
		<?php
		$title = htmlentities($_GET['v']);
		$v = $options['download_dir'].htmlentities($_GET['v']);
		$mime = mime_content_type($v);

		?>
	<h1 class="major"><?php echo $title;?></h1>
	<span class="image fit">
	  <video id='my-video' class='video-js' controls preload='auto' data-setup='{"fluid" : true, "aspectRatio" : "16:9"}'>

		<source src="<?php echo $v;?>" type='<?php echo $mime;?>'>
		<p class='vjs-no-js'>
		  To view this video please enable JavaScript, and consider upgrading to a web browser that
		  <a href='https://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
		</p>
	  </video>
	</span>
	  <!--<script src='https://vjs.zencdn.net/7.4.1/video.js'></script>
	  -->
	  </div>
	</section>
</div>
  
<?php
include(TEMPLATE_DIR.'footer.php');
?>