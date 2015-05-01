<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


 class Home extends CI_Controller {

    function index()
    {
        /*
         * Just loads a blank page
         */
        echo '';
    }

    function setup()
    {
        /*
         * Loads the login page 
         */
        
        $this->load->view('config_view');
    }

    function dovalidateLogin()
    {
        /*
         * Processes a login and validates
         * the user's credentials 
         */
        
        // Get clean info from login form
        $username = xss_clean(trim($this->input->post('username')));
        $password = xss_clean(trim($this->input->post('password')));

        // Validate username/password
        $query = $this->user_model->validate($username, $password);

        if($query) // if the user's credentials validated...
        {
            $data = array(
                'is_logged_in' => true
            );

            // Save session data
            $this->session->set_userdata($data);

            // Forward to the main config page
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/main');
        }
            else // incorrect username or password
        {
            // Invalid login, display msg
            $this->session->set_flashdata('messages', '<div class="warning2">Incorrect username or password!</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/setup');
        }
    }

    function main()
    {
        /*
         * Display the main page
         */
        
        // Ensure user is logged in
        $this->is_logged_in();

        $this->load->view('config_main_view');
    }

    function changepwd()
    {
        /*
         * Display the change password view
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        $this->load->view('config_changepwd_view');
    }

    function info()
    {
        /*
         * Display the info page 
         */
        
        // Ensure user is logged in
        $this->is_logged_in();

        // Get plugbot info
        $query = $this->plugbot_model->getPlugBotInfo();
        $row = $query->row();
        $data['plugbot_version'] = $row->plugbot_version;
        $data['plugbot_appname'] = $row->plugbot_appname;
        $data['plugbot_credit'] = $row->plugbot_credit;
        $data['plugbot_legalese'] = $row->plugbot_legalese;
        $this->load->view('config_info_view', $data);
    }

    function bot()
    {
        /*
         * Loads view for changing the bot settings
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Get dropzone settings
        $query = $this->bot_model->getBot();
        $row = $query->row();
        $data['bot_privatekey'] = $row->bot_privatekey;        
        $data['botkey'] = $row->bot_key;
        $data['botname'] = $row->bot_name;

        $this->load->view('config_bot_view', $data);
    }

    function doUpdateBot()
    {
        /*
         * Processes the update bot settings request
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        $botname = xss_clean(trim($this->input->post('botname')));
        $botkey = xss_clean(trim($this->input->post('botkey')));
        $bot_privatekey = xss_clean(trim($this->input->post('bot_privatekey')));

        if ($botkey AND $botname AND $bot_privatekey)
        {
            // Update db
            $this->bot_model->updateBot($botname, $bot_privatekey, $botkey);

            // Notify user
            $this->session->set_flashdata('messages', '<div class="success">Saved settings</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/bot');
        } else {
            // Notify user
            $this->session->set_flashdata('messages', '<div class="warning2">Error: all fields are required</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/bot');
        }
    }

    function doDeleteApp()
    {
        /*
         * Processes the deletion of an app
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Get and clean the post request
        $app_id = xss_clean(trim($this->input->post('app_id')));

        if ($app_id)
        {
            // Lookup app to get name and random
            $query = $this->app_model->getAppDetails($app_id);

            if ($query->num_rows() > 0)
            {
                // Delete directory
                $q = $query->row();

                $dir = '/var/www/apps/'.url_title($q->app_name).'.'.$q->app_random;
                $this->_recursive_remove_directory($dir);                

                // Delete record
                $this->app_model->deleteApp($q->app_id);

                // Notify user
                $this->session->set_flashdata('messages', '<div class="success">App has been deleted</div>');
                redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/viewallapps');
            } else {
                // Notify user of error
                $this->session->set_flashdata('messages', '<div class="warning2">Error: unable to delete app!</div>');
                redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/viewallapps');
            }
        } else {
            // Notify user of error
            $this->session->set_flashdata('messages', '<div class="warning2">Error: unable to delete app!</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/viewallapps');
        }

    }

    function doChangePwd()
    {
        /*
         * Processes the change password request
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Process a password change to the bot configuration web interface
        $new_password = xss_clean(trim($this->input->post('new_password')));
        $confirm_password = xss_clean(trim($this->input->post('confirm_password')));

        if ($new_password == $confirm_password)
        {
            $this->user_model->updatePassword($new_password);
            //$this->user_model->updateDropzone($new_password);

            // Notify user
            $this->session->set_flashdata('messages', '<div class="success">Password changed</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/changepwd');

        } else {
            // Notify user
            $this->session->set_flashdata('messages', '<div class="warning2">Password mismatch. Try again!</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/changepwd');
        }

    }

    function doClearLogs()
    {
        /*
         * Processes the clear logs request
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Clear logs
        $this->log_model->clearlogs();

        // Notify user
        $this->session->set_flashdata('messages', '<div class="success">Log table cleared</div>');
        redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/utilities');
    }

    function doClearJobs()
    {
        /*
         * Empties the tblJob table
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Clears out the jobs table
        $this->job_model->clearjobs();

        // Notify user
        $this->session->set_flashdata('messages', '<div class="success">Job table cleared</div>');
        redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/utilities');
    }

    function dropzone()
    {
        /*
         * Loads the view for changing dropzone settings
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Get dropzone settings
        $query = $this->dropzone_model->getDropzone();
        $row = $query->row();
        $data['dropzone_url'] = $row->dropzone_url;
        $data['dropzone_tor'] = $row->dropzone_tor;

        $this->load->view('config_dropzone_view',$data);
    }

    function doChangeDropZone()
    {
        /*
         * Processes the change dropzone settings request
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        $dropzone_url = xss_clean(trim($this->input->post('dropzone_url')));
        $dropzone_tor = xss_clean(trim($this->input->post('dropzone_tor')));

        if ($dropzone_url && $dropzone_tor)
        {
            $this->dropzone_model->updateDropZone($dropzone_url, $dropzone_tor);

            // Notify user
            $this->session->set_flashdata('messages', '<div class="success">Setting have been saved</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/dropzone');
        } else {
            // Notify user; not successful
            $this->session->set_flashdata('messages', '<div class="warning2">Error: All fields are required!</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/dropzone');
        }

    }

    function deploy()
    {
        /* 
         * Displays the deployment checklist view
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Get dropzone settings
        $query = $this->dropzone_model->getDropzone();
        $row = $query->row();
        $data['dropzone_url'] = $row->dropzone_url;

        // Get Bot settings
        $q = $this->bot_model->getBot();
        $r = $q->row();
        $data['botkey'] = $r->bot_key;
        $data['botname'] = $r->bot_name;

        // Get scheduler status
        $sched = shell_exec('crontab -l -u www-data');

        if ($sched != '')
        {
            $data['sched_status'] = 'Running';
        } else {
            $data['sched_status'] = 'Disabled!';
        }

        $this->load->view('config_deploy_view', $data);
    }

    function logout()
    {
        /*
         * Log out page
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        $this->session->sess_destroy();
        $this->session->set_flashdata('messages', '<div class="notice">You are now logged out</div>');
        redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/setup');
    }

    function diag()
    {
        /*
         * Displays the diagnostics view
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        $this->load->view('config_diag_view');
    }

    function utilities()
    {
        /*
         * Displays the utilities view
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        $this->load->view('config_utilities_view');
    }

    function viewall()
    {
        /*
         * Displays all of the logs
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        $config['base_url'] = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/viewall/';
        $config['total_rows'] = $this->db->get('tblLog')->num_rows();
        $config['per_page'] = 10;
        $config['num_links'] = 10;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['full_tag_open'] = '<div id="pagination">';
        $config['full_tag_close'] = '</div>';
        $this->pagination->initialize($config);

        $this->db->order_by('log_date','desc');
        $data['records'] = $this->db->get('tblLog', $config['per_page'], $this->uri->segment(3));
        $this->load->view('config_viewalllogs_view', $data);
    }

    function viewnci()
    {
        /*
         * Displays all non-checkin log entries
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        $config['base_url'] = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/viewnci/';
        $config['total_rows'] = $this->log_model->countNCILogs();
        $config['per_page'] = 10;
        $config['num_links'] = 10;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['full_tag_open'] = '<div id="pagination">';
        $config['full_tag_close'] = '</div>';

        $this->pagination->initialize($config);

        $this->db->not_like('log_type', '100');
        $this->db->order_by('log_date','desc');
        $data['records'] = $this->db->get('tblLog', $config['per_page'], $this->uri->segment(3));

        $this->load->view('config_viewncilogs_view', $data);
    }

    function viewalljobs()
    {
        /*
         * Displays all jobs
         */

        // Ensure user is logged in
        $this->is_logged_in();
        
        $config['base_url'] = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/viewalljobs/';
        $config['total_rows'] = $this->db->get('tblJob')->num_rows();
        $config['per_page'] = 10;
        $config['num_links'] = 10;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['full_tag_open'] = '<div id="pagination">';
        $config['full_tag_close'] = '</div>';
        $this->pagination->initialize($config);

        $this->db->order_by('job_id','desc');
        $this->db->select('job_id, job_botkey, job_name, job_command');
        $data['records'] = $this->db->get('tblJob', $config['per_page'], $this->uri->segment(3));
        
        $this->load->view('config_viewalljobs_view', $data);
    }

    function viewallapps()
    {
        /*
         * Displays all apps
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        $config['base_url'] = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/home/viewallapps/';
        $config['total_rows'] = $this->db->get('tblApp')->num_rows();
        $config['per_page'] = 10;
        $config['num_links'] = 10;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['full_tag_open'] = '<div id="pagination">';
        $config['full_tag_close'] = '</div>';
        $this->pagination->initialize($config);

        $this->db->order_by('app_id','desc');
        $data['app_data'] = $this->db->get('tblApp', $config['per_page'], $this->uri->segment(3));

        $this->load->view('config_viewallapps_view', $data);
    }

    function testCheckIn()
    {
        /*
         * Tests bot check in connectivity
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Get botkey
        $botkey = $this->bot_model->getBotKey();

        // Get URL
        $dzone_url = $this->dropzone_model->getDropURL();
         
        // Get Bot IP
        $bot_ip = $_SERVER['SERVER_ADDR'];

         // if  botkey and url and IP are valid...
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
            
            if ($xml)
            {
                $data = $xml->job[0]->status;
            } else {
                $data = '000'; // Error loading xml; set data to error out
            }
            
            // If checkin was successful
            if ($data == '200')
            {
                // successful checkin
                $this->session->set_flashdata('messages', '<div class="success">Check-In was successful!</div>');
                redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/diag');
            } else {
                // not successful check in
                $this->session->set_flashdata('messages', '<div class="warning2">Check-In did not work!</div>');
                redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/diag');
            }

        } else {
            // invalid botkey and url
            $this->session->set_flashdata('messages', '<div class="warning2">Scheduler Bot Key / URL invalid!</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/diag');
        }

    }


    function is_logged_in()
    {
        /*
         * Security check to ensure the user is logged
         * in and authorized to view the content
         */
        
        $is_logged_in = $this->session->userdata('is_logged_in');
        if(!isset($is_logged_in) || $is_logged_in != true)
        {
            $this->session->sess_destroy();
            $this->session->set_flashdata('messages', '<div class="warning2">Access is restricted!</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/setup');
        }
    }

    function restoreScheduler()
    {
        /*
         * Restores the scheduler
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Stop scheduler
        $this->_stopScheduler();

        // Copy cron.default.txt to cron.txt
        $status = copy("/var/www/cron/cron.default.txt","/var/www/cron/cron.txt");

        if ($status)
        {
            // Re-start scheduler
            $this->_startScheduler();

            $this->session->set_flashdata('messages', '<div class="success">Scheduler has been restored</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/deploy');
        } else {
            $this->session->set_flashdata('messages', '<div class="warning2">Unable to restore the scheduler!</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/deploy');                  
        }

    }

    function restoreDiagScheduler()
    {
        /*
         * Resarts the scheduler 
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Stop scheduler
        $this->_stopScheduler();

        // Copy cron.default.txt to cron.txt
        $status = copy("/var/www/cron/cron.default.txt","/var/www/cron/cron.txt");

        if ($status)
        {
            // Re-start scheduler
            $this->_startScheduler();

            $this->session->set_flashdata('messages', '<div class="success">Scheduler has been restored</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/diag');
        } else {
            $this->session->set_flashdata('messages', '<div class="warning2">Unable to restore the scheduler!</div>');
            redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/diag');
        }

    }

    function stopDiagScheduler()
    {
        /*
         * Stops the scheduler (cron job)
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Stop scheduler
        $this->_stopScheduler();

        $this->session->set_flashdata('messages', '<div class="success">Scheduler has been stopped</div>');
        redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/diag');        
    }

    function startDiagScheduler()
    {
        /*
         * Starts the scheduler (cron job)
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Start scheduler
        $this->_startScheduler();

         $this->session->set_flashdata('messages', '<div class="success">Scheduler was started</div>');
        redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/diag');
    }


    function disableScheduler()
    {
        /*
         * Disables the scheduler
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        // Stop scheduler
        $this->_stopScheduler();

        $this->session->set_flashdata('messages', '<div class="success">The scheduler has been STOPPED</div>');
        redirect('http://'.$_SERVER['SERVER_NAME'].'/index.php/home/deploy');
    }

    function _stopScheduler()
    {
        /* 
         * Stops cron job and bot from checking in
         */
        
        // Ensure user is logged in
        $this->is_logged_in();
        
        system('crontab -u www-data -r');
    }

    function _startScheduler()
    {
        /*
         * Starts cron and enables the bot to start checking in
         */
        
        // Ensure user is logged in
        $this->is_logged_in();        
        
        system('crontab -u www-data /var/www/cron/cron.txt');

    }

    function _recursive_remove_directory($directory, $empty=FALSE)
    {
        /*
         * When an app is deleted, this ensures all subfolders
         * are deleted recursively 
         */
        
        // Ensure user is logged in
        $this->is_logged_in();        
        
        if(substr($directory,-1) == '/')
        {
            $directory = substr($directory,0,-1);
        }
        if(!file_exists($directory) || !is_dir($directory))
        {
            return FALSE;
        }elseif(is_readable($directory))
        {
            $handle = opendir($directory);
            while (FALSE !== ($item = readdir($handle)))
            {
                if($item != '.' && $item != '..')
                {
                    $path = $directory.'/'.$item;
                    if(is_dir($path))
                    {
                        $this->_recursive_remove_directory($path);
                    }else{
                        unlink($path);
                    }
                }
            }
            closedir($handle);
            if($empty == FALSE)
            {
                if(!rmdir($directory))
                {
                    return FALSE;
                }
            }
        }
        return TRUE;
    }
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */

