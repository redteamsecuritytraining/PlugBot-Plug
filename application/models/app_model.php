<?php
 
class App_model Extends CI_Model {

    function insertApp($botkey, $app_random, $app_path, $app_exec, $app_name)
    {
        /*
         * Adds a new app to the bot
         */
        
        $app = array(
            'app_botkey'    =>      $botkey,
            'app_random'    =>      $app_random,
            'app_dir'       =>      $app_path,
            'app_exec'      =>      $app_exec,
            'app_name'      =>      $app_name
        );

        $this->db->insert('tblApp', $app);
    }

    function getApp($botkey, $app_random)
    {
        /*
         * Gets app record
         */
        $this->db->where('app_random',$app_random);
        $this->db->where('app_botkey', $botkey);
        return $this->db->get('tblApp');
    }

    function getAppDetails($id)
    {
        /*
         * Gets the apps details
         */
        
        $this->db->where('app_id',$id);
        return $this->db->get('tblApp');
    }

    function deleteApp($id)
    {
        /*
         * Simply deletes the app
         */
        
        $this->db->where('app_id', $id);
        $this->db->delete('tblApp');
    }

}