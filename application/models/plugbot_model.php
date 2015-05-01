<?php

class Plugbot_model Extends CI_Model {
    
    function __construct()
    {
        parent::__construct();
    }
    
    function getPlugBotInfo()
    {
        /*
         * Gets the info for the PlugBot
         * NOTE: there should only be 1 record in the tblPlugbot table
         */
        
        $this->db->where('plugbot_id', '0');
        $query = $this->db->get('tblPlugBot');
        return $query;
    }
}


