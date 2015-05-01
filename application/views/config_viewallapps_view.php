<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Config - View All Apps</title>
        <link rel="stylesheet" href="<?php echo 'http://'.$_SERVER['SERVER_NAME'].'/css/style.css'; ?>" type="text/css" media="screen" />
</head>

<body>
<div id="submenu"><a href="logout">Logout</a></div>
<div id="log_form">

    <h1>Configuration</h1>
    <div align="center"><a href="<?php echo 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/main'; ?>">Home</a></div><br>
    <div align="center"><a href="<?php echo 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/utilities'; ?>">Back</a></div>
    <div class="info">View All Apps (<?php echo $this->db->count_all('tblApp'); ?> entries)</div>
    <?php echo $this->session->flashdata('messages'); ?>

         <div id="container">
                <?php //$this->table->set_heading(array('ID', 'Botkey', 'Name','Directory', 'Exec','lskd')); ?>
		<?php //echo $this->table->generate($records); ?>
		<?php //echo $this->pagination->create_links(); ?>
             <table>
              <tr>
                <th>ID</th>
                <th>Botkey</th>
                <th>Name</th>
                <th>Directory</th>
                <th>Exec</th>
                <th>Action</th>
              </tr>
              <?php foreach($app_data->result() as $app): ?>
                  <tr>
                    <td><?php echo $app->app_id; ?></td>
                    <td><?php echo $app->app_botkey; ?></td>
                    <td><?php echo $app->app_name; ?></td>
                    <td><?php echo $app->app_dir; ?></td>
                    <td><?php echo $app->app_exec; ?></td>
                    <td><form method="post" action="doDeleteApp"><input type="hidden" value="<?php echo $app->app_random; ?>" name="random" id="random"><input type="hidden" value="<?php echo $app->app_id ?>" name="app_id" id="app_id"><input type="submit" value="Delete"></form></td>
                  </tr>
              <?php endforeach; ?>
            </table>
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
