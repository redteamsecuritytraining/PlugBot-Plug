<?php

class Dropzone_model Extends CI_Model {
    
    function __construct()
    {
        parent::__construct();
    }
    
    function getDropURL()
    {
        /*
         * Used to build the URL for bot check-in
         */
        
        $this->db->where('dropzone_id','1');
        $query = $this->db->get('tblDropZone');
        
        if ($query->num_rows() == 1)
        {
            $row = $query->row();
            $url = $row->dropzone_url;
            return $url;
        } else {
            return '';
        }
    }

    function getDropZone()
    {
        /*
         * Get's the dropzone information
         * there is only one record for this
         */
        
        $this->db->where('dropzone_id', '1');
        $query = $this->db->get('tblDropZone');
        return $query;        
    }
    
    function getTor()
    {
        /*
         * Get the Tor setting
         */
        
        $this->db->where('dropzone_id','1');
        $query = $this->db->get('tblDropZone');
        
        if ($query->num_rows() == 1)
        {
            $row = $query->row();
            $item = $row->dropzone_tor;
            return $item;
        } else {
            return '';
        }
    }    
    
    function updateTor($dropzone_tor)
    {
        /*
         * Update the Tor setting
         */
        
        $data = array(
                'dropzone_tor' => $dropzone_tor
            );

        $this->db->update('tblDropZone', $data);
    }
    
    function updateDropzone($dropzone_url, $dropzone_tor)
    {
        /*
         * Makes the updates to the dropzone table with 
         * any new settings  
         */
        
        $data = array(
                'dropzone_url' => $dropzone_url,
                'dropzone_tor' => $dropzone_tor
            );

        $this->db->update('tblDropZone', $data);
    }
    
    function updateDropzoneURL($dropzone_url)
    {
        /*
         * Makes the updates to the dropzone URL
         */
        
        $data = array(
                'dropzone_url' => $dropzone_url
            );

        $this->db->update('tblDropZone', $data);
    }
    
    
}

