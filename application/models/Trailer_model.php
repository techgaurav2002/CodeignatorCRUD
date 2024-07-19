<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trailer_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function create_trailer($data) {
        try{
        return $this->db->insert('trailer', $data);
        }catch(Exception $e){
            echo $e;
        }
    }

    public function get_trailer($trailer_id) {
        try{
        if($trailer_id === Null){
            $query = $this->db->get('trailer');
            return $query->result_array();
        }
        return $this->db->get_where('trailer', ['trailer_id' => $trailer_id])->row_array();
    }catch(Exception $e){
        echo $e;
    }
    }

    public function update_trailer($trailer_id, $data) {
        try{
        $this->db->where('trailer_id', $trailer_id);
        return $this->db->update('trailer', $data);
        }catch(Exception $e){
            echo $e;
        }
    }

    public function delete_trailer($trailer_id) {
        try{
        $this->db->where('trailer_id', $trailer_id);
        return $this->db->delete('trailer');
        }catch(Exception $e){
            echo $e;
        }
    }
}
