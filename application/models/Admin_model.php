<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    public function register($data)
    {
        return $this->db->insert('admin', $data);
    }

    public function login($email, $password)
    {
        $this->db->where('email', $email);
        $this->db->where('password', $password);
        $query = $this->db->get('admin');

        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function email_exists($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('admin');

        return $query->num_rows() > 0;
    }
}
?>
