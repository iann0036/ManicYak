<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>Login  |  Manic Yak</title>
<link href="/css/main.css" rel="stylesheet" type="text/css" />
<!--[if IE]> <link href="/css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery_ui_custom.js"></script>

<script type="text/javascript" src="/js/plugins/charts/excanvas.min.js"></script>
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
</head>

<body>
<div id="fb-root"></div>
<div class="login-wrapper">
    <div class="login">
        <a href="/" title="" class="login-logo"><img src="/images/login-logo.png" alt="" /></a>
		
		<?php
			if (isset($alert))
				echo '<div class="notice outer"><div class="note note-'.$alert['type'].'"><button type="button" class="close">×</button>'.$alert['message'].'</div></div>';
		?>
		
        <div class="well">
            <div class="navbar">
                <div class="navbar-inner">
                    <h6><i class="font-user"></i>Login</h6>
                    <div class="nav pull-right">
                        <a href="#" class="dropdown-toggle just-icon" data-toggle="dropdown"><i class="font-cog"></i></a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="/register"><i class="font-plus"></i>Register</a></li>
                            <li><a href="/recover"><i class="font-refresh"></i>Recover password</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <form action="/" method="post" class="row-fluid">
				<div class="login-btn"><a href="/fb/login/"><button type="button" class="btn btn-info btn-block btn-large"><i class="font-facebook-sign"></i>Login with Facebook</button></a></div>
				<div class="separator-doubled"></div>
                <div class="control-group">
                    <label class="control-label">Username:</label>
                    <div class="controls"><input class="span12" type="text" name="username" placeholder="username" /></div>
                </div>
                
                <div class="control-group">
                    <label class="control-label">Password:</label>
                    <div class="controls"><input class="span12" type="password" name="password" placeholder="password" /></div>
                </div>

                <div class="control-group">
                    <div class="controls"><label class="checkbox inline"><input type="checkbox" name="remember" class="style" value="" checked="checked">Remember me</label></div>
                </div>

                <div class="login-btn"><input type="submit" value="Login" class="btn btn-info btn-block btn-large" /></div>
            </form>
        </div>

    </div>

</div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-50859151-1', 'manicyak.com');
  ga('send', 'pageview');

</script>
</body>
</html>
