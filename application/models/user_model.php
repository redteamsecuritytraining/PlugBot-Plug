<?php

class User_model Extends CI_Model {
    
    
    function validate($username, $password)
    {
        /*
         * Validate the user's username and password
         */
        
        $this->db->where('user_username', $username);
        $this->db->where('user_password', do_hash($password.$this->_salt())); // SHA 1 hash with salt
        $query = $this->db->get('tblUser');

        if($query->num_rows == 1)
        {
            return true;
        }
    }

    function updatePassword($password)
    {
        /*
         * Simply performs an update of the password
         */
        
        $data = array(
                'user_password' =>  do_hash($password.$this->_salt()) // Only 1 record in the tblUser exists
            );

        $this->db->where('user_id', '1');
        $this->db->update('tblUser', $data);
    }

    private function _salt()
    {
        /*
         * For now, this is a static salt 
         */
        
        $salt = 'fhe(#984)!~ifruh*s%i4ur@gir)(HSJh39(*RhaaaQ~`Ufh;adj';
        return $salt;
    }
}
