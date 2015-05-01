<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Config - Change Password</title>
        <link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen" />
</head>

<body>
<div id="submenu"><a href="logout">Logout</a></div>
<div id="main_form">

    <h1>Configuration</h1>
    <div align="center"><a href="main">Home</a></div>
    <div class="info">Change Bot Password</div>
    <?php echo $this->session->flashdata('messages'); ?>
    <form method="post" action="<?php echo 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/doChangePwd'; ?>">
        <label>New password</label> <a href="#" class="tooltip">[?]<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>New Password</em>Enter the new password. There are no limitations on password strength but you should choose a secure password.</span></a>
        <input type="password" id="new_password" name="new_password" value="" />
        <label>Confirm password</label> <a href="#" class="tooltip">[?]<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>Confirm New Password</em>Confirm the new password</span></a>
        <input type="password" id="confirm_password" name="confirm_password" value="" />
        <input type="submit" value="Save" />
    </form>
</div>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>

    <script type="text/javascript" charset="utf-8">
            $('input').click(function(){
                    $(this).select();
            });
    </script>

</body>
</html>
