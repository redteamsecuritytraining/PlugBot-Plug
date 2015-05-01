<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>&#216;</title>
        <link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen" />
</head>

<body>

<div id="login_form">

    <h1>Login</h1>
        <?php echo $this->session->flashdata('messages'); ?>
        <?php $this->session->sess_destroy(); ?>
        <form method="post" action="<?php echo 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/dovalidateLogin'; ?>">
            <input type="text" name="username" id="username" value=""/>
            <input type="password" name="password" id="password" value=""/>
            <input type="submit" value="Login" />
        </form>

</div><!-- end login_form-->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>

    <script type="text/javascript" charset="utf-8">
            $('input').click(function(){
                    $(this).select();
            });
    </script>

</body>
</html>


