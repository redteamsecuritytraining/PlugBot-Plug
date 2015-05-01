<?php

class Log_model extends CI_Model {

    function log_action($bk, $type, $action)
    {
        /*
         * Used to add log entries 
         */
        
        $log = array(
        'log_date'      =>  date("Y-m-d H:i:s"),
        'log_botkey'    =>  $bk, // ID of the bot
        'log_type'      =>	$type,	// Code
        'log_action'    => 	$action // Action that took place
        );

        $this->db->insert('tblLog', $log);
    }

    function clearlogs()
    {
        /*
         * Truncates the table 
         */
        
        $this->db->truncate('tblLog');
    }


    function getNCILogs()
    {
        /* 
         * Gets all Log entries, except for CHECK-INs (Type 100)
         */
        
    	$this->db->order_by("log_date", "desc");
        $this->db->where('log_type !=', '100'); // Do NOT get CHECK-IN log entries
        return $this->db->get('tblLog');
    }

    function countNCILogs()
    {
        /* 
         * Count all Log entries, except CHECK-INs
         */
        
       $this->db->from('tblLog');
       $this->db->not_like('log_type', '100');
       return $this->db->count_all_results();
    }
    
}


