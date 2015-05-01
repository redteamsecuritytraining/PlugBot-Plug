<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Config - About This Bot</title>
        <link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen" />
</head>

<body>
<div id="submenu"><a href="logout">Logout</a></div>
<div id="main_form">

    <h1>Configuration</h1>
    <div align="center"><a href="main">Home</a></div>
    <div class="info">About This Bot</div>
    <?php echo $this->session->flashdata('messages'); ?>
    <form>
        <label>Bot Version:</label><br>
        <?php echo $plugbot_appname.' '.$plugbot_version; ?><br><br>
        <label>Credits:</label><br>
        <?php echo $plugbot_credit; ?><br><br>
        <label>Legalese:</label><br>
        <?php echo $plugbot_legalese; ?>
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
