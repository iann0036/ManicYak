<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title><?php echo $title; ?>  |  Manic Yak</title>
<link href="/css/main.css" rel="stylesheet" type="text/css" />
<!--[if IE]> <link href="/css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery_ui_custom.js"></script>

<script type="text/javascript" src="/js/plugins/charts/excanvas.min.js"></script>
<script type="text/javascript" src="/js/plugins/charts/jquery.flot.js"></script>
<script type="text/javascript" src="/js/plugins/charts/jquery.flot.orderBars.js"></script>
<script type="text/javascript" src="/js/plugins/charts/jquery.sparkline.min.js"></script>

<script type="text/javascript" src="/js/plugins/forms/jquery.tagsinput.min.js"></script>
<script type="text/javascript" src="/js/plugins/forms/jquery.inputlimiter.min.js"></script>
<script type="text/javascript" src="/js/plugins/forms/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="/js/plugins/forms/jquery.autosize.js"></script>
<script type="text/javascript" src="/js/plugins/forms/jquery.ibutton.js"></script>
<script type="text/javascript" src="/js/plugins/forms/jquery.dualListBox.js"></script>
<script type="text/javascript" src="/js/plugins/forms/jquery.validate.js"></script>
<script type="text/javascript" src="/js/plugins/forms/jquery.uniform.min.js"></script>
<script type="text/javascript" src="/js/plugins/forms/jquery.select2.min.js"></script>
<script type="text/javascript" src="/js/plugins/forms/jquery.cleditor.js"></script>

<script type="text/javascript" src="/js/plugins/uploader/plupload.js"></script>
<script type="text/javascript" src="/js/plugins/uploader/plupload.html4.js"></script>
<script type="text/javascript" src="/js/plugins/uploader/plupload.html5.js"></script>
<script type="text/javascript" src="/js/plugins/uploader/jquery.plupload.queue.js"></script>

<script type="text/javascript" src="/js/plugins/wizard/jquery.form.wizard.js"></script>
<script type="text/javascript" src="/js/plugins/wizard/jquery.form.js"></script>

<script type="text/javascript" src="/js/plugins/ui/jquery.collapsible.min.js"></script>
<script type="text/javascript" src="/js/plugins/ui/jquery.timepicker.min.js"></script>
<script type="text/javascript" src="/js/plugins/ui/jquery.jgrowl.min.js"></script>
<script type="text/javascript" src="/js/plugins/ui/jquery.pie.chart.js"></script>
<script type="text/javascript" src="/js/plugins/ui/jquery.fullcalendar.min.js"></script>
<script type="text/javascript" src="/js/plugins/ui/jquery.elfinder.js"></script>
<script type="text/javascript" src="/js/plugins/ui/jquery.fancybox.js"></script>

<script type="text/javascript" src="/js/plugins/tables/jquery.dataTables.min.js"></script>

<script type="text/javascript" src="/js/plugins/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="/js/plugins/bootstrap/bootstrap-bootbox.min.js"></script>
<script type="text/javascript" src="/js/plugins/bootstrap/bootstrap-progressbar.js"></script>
<script type="text/javascript" src="/js/plugins/bootstrap/bootstrap-colorpicker.js"></script>

<script type="text/javascript" src="/js/functions/custom.js"></script>
<script type="text/javascript" src="/js/functions/manicyak.js"></script>

</head>
<body>
<div id="top">
    <div class="top-wrapper">
        <a href="/" title="Manic Yak"><img src="/images/logo.png" class="logo" alt="Manic Yak" /></a>
        <ul class="topnav">
            <li class="topuser">
                <a href="/profile/" title="<?php echo $realname ?>"><img style="max-width: 28px; max-height: 28px;" src="<?php if ($this->session->userdata('type')=='internal') echo '/images/avatars/'.$username.'.png'; else echo 'https://graph.facebook.com/'.$this->session->userdata('username').'/picture?type=square'; ?>" alt="" /><span>&nbsp;<?php echo $realname ?></span></a>
            </li>
			
            <li><a href="/settings" title=""><b class="settings"></b></a></li>
            <li><a href="/logout" title=""><b class="logout"></b></a></li>
			<li class="sidebar-button"><a href="#" title=""><b class="responsive-nav"></b></a></li>
        </ul>
    </div>
</div>

<div class="wrapper">
    <div class="sidebar" id="left-sidebar">
		<form action="/search/" method="post" class="side-search block">
			<input name="q" type="text" value="" placeholder="Search..." />
			<input type="submit" value="" />
		</form>
		
        <ul class="navigation standard">
            <li<?php if ($title=="Dashboard") echo ' class="active"'; ?>><a href="/" title=""><img src="/images/icons/mainnav/dashboard.png" alt="" />Dashboard</a></li>
            <li<?php if ($title=="Search") echo ' class="active"'; ?>><a href="/search/" title=""><img src="/images/icons/mainnav/search.png" alt="" />Search</a></li>
			<li<?php if ($title=="Media") echo ' class="active"'; ?>><a href="/media/" title=""><img src="/images/icons/mainnav/media.png" alt="" />Media</a></li>
			<li<?php if ($title=="Popular") echo ' class="active"'; ?>><a href="#" title="" class="expand"<?php if ($title=="Popular") echo ' id="current"'; ?>><img src="/images/icons/mainnav/charts.png" alt="" />Popular</a>
                <ul>
                    <li><a href="/top/movie/" title="">Movies</a></li>
                    <li><a href="/top/music/" title="">Music</a></li>
                    <li><a href="/top/tv/" title="">TV</a></li>
                </ul>
            </li>
			<li<?php if ($title=="Settings") echo ' class="active"'; ?>><a href="/settings/" title=""><img src="/images/icons/mainnav/settings.png" alt="" />Settings</a></li>
            <li><a href="/logout/" title=""><img src="/images/icons/mainnav/logout.png" alt="" />Logout</a></li>
            
        </ul>
		<!--
		<ul class="text-stats block">
			<li>2<span>gathers</span></li>
			<li>0<span>favorites</span></li>
			<li>0<span>subscribes</span></li>
		</ul>
		-->
		<ul id="sidebar" style="display: none;" class="block progress-statistics">
		</ul>
    </div>
	
    <div class="content">
		<?php
		if (isset($alert))
			echo '<div class="notice outer"><div class="note note-'.$alert['type'].'"><button type="button" class="close">×</button>'.$alert['message'].'</div></div>';
		?>
    	<div class="outer">
        	<div class="inner">
                <div class="page-header">
				<?php
					switch ($title) {
						case "Dashboard":
							echo '<h5><i class="font-home"></i>Dashboard</h5>';
							break;
						case "Media":
							echo '<h5><i class="font-globe"></i>Media</h5>';
							break;
						case "Settings":
							echo '<h5><i class="font-cog"></i>Settings</h5>';
							break;
						case "Profile":
							echo '<h5><i class="font-edit"></i>Profile</h5>';
							break;
						case "Search":
							echo '<h5><i class="font-search"></i>Search</h5>';
							break;
						case "Popular":
							echo '<h5><i class="font-bar-chart"></i>Popular</h5>';
							break;
						
					}
				?>
                </div>
                
                <div class="body">

                    <div class="container">
					
                