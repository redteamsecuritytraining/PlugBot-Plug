<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Config - DropZone</title>
        <link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen" />
</head>

<body>
<div id="submenu"><a href="logout">Logout</a></div>
<div id="main_form">

    <h1>Configuration</h1>
    <div align="center"><a href="main">Home</a></div>
    <?php echo $this->session->flashdata('messages'); ?>

    <form method="post" action="<?php echo 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/doChangeDropZone'; ?>">
       
        <div class="info">DropZone URL</div>
        <label>DropZone URL</label> <a href="#" class="tooltip">[?]<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>DropZone URL</em>The DropZone URL is the location on the Internet where you have installed the Command & Control. Do not append a backslash to the url. For example: http://www.site.com/dropzone</span></a>
        <input type="text" id="dropzone_url" name="dropzone_url" value="<?php echo $dropzone_url; ?>" />

        <div class="info">Tor Configuration</div>
        <label>Tor enabled?</label> <a href="#" class="tooltip">[?]<span class="custom help"><img src="../../img/Help.png" alt="Help" height="48" width="48" /><em>Tor enabled</em>Selecting Yes will anonymize all outbound connections through the Tor network.</span></a>
        <br/><select name="dropzone_tor" id="dropzone_tor">
                <option value="<?php echo $dropzone_tor; ?>"><?php if($dropzone_tor == '1'){ echo 'No'; } else { echo 'Yes'; } ?></option>
                <option></option>
                <option value="1">No</option>
                <option value="2">Yes</option>
        </select>
        <br/><br/>
        <input type="submit" value="Save" />
    </form>
</div>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
</body>
</html>
