<?php

Class Bot_model Extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    
    function getBotKey()
    {
        /*
         *  Gets the Botkey
         *  NOTE: thers should be only 1 record in tblBot table
         */

        $this->db->where('bot_id','1');
        $query = $this->db->get('tblBot');
        
        if ($query->num_rows() == 1)
        {
            $row = $query->row();
            $item = $row->bot_key;
            return $item;
        } else {
            return '';
        }            
            
    }

    function getBot()
    {
        /*
         * Gets all information about the bot
         */
        
        $this->db->where('bot_id','1'); // Get first record; should only be one
        return $this->db->get('tblBot');     
    }

    function updateBot($botname, $bot_privatekey, $botkey)
    {
        /*
         * Processes any updates to the bot settings
         */
        
        $data = array(
                'bot_name' => $botname,
                'bot_privatekey' => $bot_privatekey,
                'bot_key' => $botkey
            );

        $this->db->where('bot_id', '1');
        $this->db->update('tblBot', $data);
    }

    function getBotPrivateKey()
    {
        /*
         * Returns the bot's private key
         */
        
        $this->db->where('bot_id','1');
        $query = $this->db->get('tblBot');
        
        if ($query->num_rows() == 1)
        {
            $row = $query->row();
            $item = $row->bot_privatekey;
            return $item;
        } else {
            return '';
        }            
    }
    
}

