<?php

class Job_model Extends CI_Model {

    function insertJob($botkey, $job_name, $job_app_random, $job_random, $job_cmd, $job_output, $job_id)
    {
        /* 
         * Inserts a new job into the Job table. Also
         * sets the status to '1' indicating that its
         * a new job to be picked up
         */
        
        $job = array(
            'job_status'        =>      '1', // Set status to '1'; aka pending job...
            'job_botkey'        =>      $botkey,
            'job_name'          =>      $job_name,
            'job_app_random'    =>      $job_app_random,
            'job_random'        =>      $job_random,
            'job_command'       =>      $job_cmd,
            'job_output'        =>      $job_output,
            'job_updateid'      =>      $job_id // Used to ref the record ID for the job at the C&C db
        );

        $this->db->insert('tblJob', $job);
    }

    function getAllNewJobs($botkey)
    {
        /*
         * Gathers all new jobs 
         */
        
        $this->db->order_by('job_id', 'desc');
        $this->db->where('job_status', 1);
        $this->db->where('job_botkey', $botkey);
        return $this->db->get('tblJob');
    }

    function updateJobComplete($job_id)
    {
        /*
         * Updates the job as complete in the local table 
         */
        
        $data = array(
                'job_status' => '2' // '2' represents a job that has been exectued
            );

        $this->db->where('job_id', $job_id);
        $this->db->update('tblJob', $data);
    }

    function clearjobs()
    {
        /*
         * Deletes all records in the Job table
         */
        
        $this->db->empty_table('tblJob');
    }

}
