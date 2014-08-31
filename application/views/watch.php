<!DOCTYPE html>
<html>
<head>
  <title>View  |  Manic Yak</title>
  <link href="/css/video-js.css" rel="stylesheet" type="text/css">
  <script src="/js/video.js"></script>
  <script>
    videojs.options.flash.swf = "/js/video-js.swf";
	
	window.onresize = function() {
		document.getElementById('video').width = window.width;
		document.getElementById('video').height = window.height;
	}
	
	
  </script>
  <style>
  body {
	margin: 0;
	padding: 0;
	overflow: hidden;
	background: black;
  }
  
  #video { left:0; right:0; top:0; bottom:0; }
  </style>
</head>
<body>
  <video id="video" class="video-js vjs-default-skin" controls preload="auto" autoplay="autoplay" width="640" height="480"
      data-setup="{}">
    <source src="/watch/source/<?php echo $id; ?>" type='video/mp4' />
  </video>
</body>
</html>
