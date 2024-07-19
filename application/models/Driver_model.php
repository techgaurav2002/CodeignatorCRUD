<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Driver_model extends CI_Model {
    public function __construct() {
        $this->load->database();
    }

    public function create_driver($data) {
        try{
        if ($this->db->insert('driver', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }
    catch(Exception $e){
        echo $e;
    }
    }

    public function get_all_drivers() {
        try{
        $query = $this->db->get('driver');
        return $query->result_array();
        }catch(Exception $e){
            echo $e;
        }
    }

    public function get_driver_by_id($id) {
        try{
        $query = $this->db->get_where('driver', array('driver_id' => $id));
        return $query->row_array();
        }catch(Exception $e){
            echo $e;
        }
    }

    public function update_driver($id, $data) {
        try{
        $this->db->where('driver_id', $id);
        return $this->db->update('driver', $data);
        }catch(Exception $e){
            echo $e;
        }
    }

    public function delete_driver($id) {
        try{
        $this->db->where('driver_id', $id);
        return $this->db->delete('driver');
        }
        catch(Exception $e){
            echo $e;
        }
    }
}
