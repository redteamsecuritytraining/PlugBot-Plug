<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Config - Deployment</title>
        <link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen" />
</head>

<body>
<div id="submenu"><a href="logout">Logout</a></div>
<div id="main_form">

    <h1>Checklist</h1>
    <div align="center"><a href="main">Home</a></div>
    <?php echo $this->session->flashdata('messages'); ?>
    <form>
        <?php
            if ($sched_status == 'Running') {
        ?>
            <p>Scheduler is <a href="#" class="tooltip">[?]<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>Scheduler Status</em>The Scheduler is currently: <b>Enabled</b>. It is ready for deployment.</span></a> | <a href="disableScheduler" class="tooltip">[TURN OFF]<span class="custom warning"><img src="../../img/Warning.png" alt="Warning" height="48" width="48" /><em>Are You Sure?</em>Are you sure you want to turn off the Scheduler? If so, be sure to turn this on BEFORE you physically deploy this bot. Failure to do so will result in the bot not functioning!</span></a></p>
        <?php } ?>

        <?php if($sched_status == 'Disabled!') {
        ?>
            <div class="notice">Scheduler is <a href="#" class="tooltip">Disabled!<span class="custom warning"><img src="../../img/Warning.png" alt="Warning" height="48" width="48" /><em>Scheduler Status</em>The Scheduler is currently: <b>Disabled!</b> You must start the scheduler BEFORE you physically deploy this bot. Failure to do so will result in the bot not functioning!</span></a> </div>
        <?php } ?>
        
        <p>Bot Key is <a href="#" class="tooltip">[?]<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>Bot Key</em>The bot key is: <b><?php echo $botkey; ?></b></span></a></p>
        <p>Bot name is <a href="#" class="tooltip">[?]<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>Bot Name</em>The bot name is: <b><?php echo $botname; ?></b></span></a></p>
        <p>DropZone URL is <a href="#" class="tooltip">[?]<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>DropZone URL</em>The DropZone URL is: <b><?php echo $dropzone_url; ?></b></span></a></p>
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
