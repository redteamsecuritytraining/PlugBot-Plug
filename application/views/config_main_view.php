<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Config Main</title>
        <link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen" />
</head>

<body>
<div id="submenu"><a href="logout">Logout</a></div>    
<div id="main_form">

    <h1>Configuration</h1>

    <div class="info">Setup Options</div>
    <div align="center">
        <p><a href="dropzone">Configure the DropZone Settings</a></p>
        <p><a href="bot">Configure the Bot Settings</a></p>
        <p><a href="changepwd">Change the Admin Password</a></p>
    </div>
    <div class="info">Deployment</div>
    <div align="center">
        <p><a href="deploy">Deployment Checklist</a></p>
        <p><a href="diag">Diagnostics</a></p>
        <p><a href="utilities">Utilities</a></p> 
        <br>
        <p><a href="info">About This Bot</a></p>
    </div>
</div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" charset="utf-8">
        $('input').click(function(){
                $(this).select();
        });
</script>

</body>
</html>


