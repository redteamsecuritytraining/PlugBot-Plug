<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Config - Diagnostics</title>
        <link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen" />
</head>

<body>
<div id="submenu"><a href="logout">Logout</a></div>
<div id="main_form">

    <h1>Diagnostics</h1>
    <div align="center"><a href="main">Home</a></div>
    <div class="info">Bot Diagnostics</div>
    <?php echo $this->session->flashdata('messages'); ?>
        <p><a href="testCheckIn" class="tooltip">Test Bot Check-In Connectivity<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>Check-In Connectivity</em>Click to test this bot's Check-In connectivity. If successful, your DropZone URL setting and Bot Key settings are correctly configured.</span></a></p>
        <p><a href="stopDiagScheduler" class="tooltip">Stop the Scheduler<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>Stop Scheduler</em>Click to stop the Scheduler. You must restart the Scheduler before you physically deploy the bot.</span></a></p>
        <p><a href="startDiagScheduler" class="tooltip">Start the Scheduler<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>Start Scheduler</em>Click to start the Scheduler. This will start the Scheduler with the current settings.</span></a></p>
</div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" charset="utf-8">
        $('input').click(function(){
                $(this).select();
        });
</script>

</body>
</html>
