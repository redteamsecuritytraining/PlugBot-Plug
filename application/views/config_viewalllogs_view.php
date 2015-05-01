<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Config - View All Logs</title>
        <link rel="stylesheet" href="<?php echo 'http://'.$_SERVER['SERVER_NAME'].'/css/style.css'; ?>" type="text/css" media="screen" />
</head>

<body>
<div id="submenu"><a href="logout">Logout</a></div>
<div id="log_form">

    <h1>Configuration</h1>
    <div align="center"><a href="<?php echo 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/main'; ?>">Home</a></div><br>
    <div align="center"><a href="<?php echo 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/utilities'; ?>">Back</a></div>
    <div class="info">View All Logs (<?php echo $this->db->count_all('tblLog'); ?> entries)</div>
    <?php echo $this->session->flashdata('messages'); ?>

         <div id="container">
                <?php $this->table->set_heading(array('ID', 'Botkey', 'Date','Type','Action')); ?>
		<?php echo $this->table->generate($records); ?>
		<?php echo $this->pagination->create_links(); ?>
	 </div>

</div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" charset="utf-8">
        $('input').click(function(){
                $(this).select();
        });
</script>

<script type="text/javascript" charset="utf-8">
        $('tr:odd').css('background', '#e3e3e3');
</script>

</body>
</html>
