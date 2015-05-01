<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Config - Bot Settings</title>
        <link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen" />
</head>

<body>
<div id="submenu"><a href="logout">Logout</a></div>
<div id="main_form">

    <h1>Configuration</h1>
    <div align="center"><a href="main">Home</a></div>
    <div class="info">Change Bot Settings</div>
    <?php echo $this->session->flashdata('messages'); ?>
    <form method="post" action="<?php echo 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/doUpdateBot'; ?>">
        <label>Bot Name</label> <a href="#" class="tooltip">[?]<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>Bot Name</em>Give the bot a descriptive name. This is especially useful if you are using several bots.</span></a>
        <input type="text" id="botname" name="botname" value="<?php echo $botname; ?>" />
        <label>Bot Key</label> <a href="#" class="tooltip">[?]<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>Bot Key</em>Enter the <u>same</u> bot key as it appears in Command & Control. If you haven't yet registered this bot in Command & Control, please do so. It is extremely crucial the bot key here matches the C&C botkey.</span></a>
        <input type="text" id="botkey" name="botkey" value="<?php echo $botkey; ?>" />
        <label>Bot Private Key</label> <a href="#" class="tooltip">[?]<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>Bot Private Key</em>Enter the <u>same</u> private key as it appears in Command & Control. If you haven't yet registered this bot in Command & Control, please do so. It is extremely crucial the bot private key here matches the C&C botkey.</span></a>
        <input type="text" id="bot_privatekey" name="bot_privatekey" value="<?php echo $bot_privatekey; ?>" />
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
