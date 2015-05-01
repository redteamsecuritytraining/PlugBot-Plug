<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Config - Utilities</title>
        <link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen" />
</head>

<body>
<div id="submenu"><a href="logout">Logout</a></div>
<div id="main_form">

    <h1>Utilities</h1>
    <div align="center"><a href="main">Home</a></div>
    <div class="info">Jobs</div>
    <?php echo $this->session->flashdata('messages'); ?>
    <p><a href="viewalljobs">View all jobs</a></p>
    <p><a href="doClearJobs" onclick="return confirm('Are you sure you want to clear all jobs?')" >Clear jobs table</a></p>
    <br />
   <div class="info">Logs</div>
    <p><a href="viewall">View all log entries</a></p>
    <p><a href="viewnci">Supress CHECK-IN log entries</a></p>
    <p><a href="doClearLogs" onclick="return confirm('Are you sure you want to clear all logs?')" >Clear log table</a></p>
    <br />
    <div class="info">Apps</div>
    <p><a href="viewallapps">Manage apps</a></p>
</div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" charset="utf-8">
        $('input').click(function(){
                $(this).select();
        });
</script>

</body>
</html>
