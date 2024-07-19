<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Truck_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_all_trucks() {
        try{
        $query = $this->db->get('truck');
        return $query->result_array();
        }catch(Exception $e){
            echo $e;
        }
    }

    public function get_truck_by_id($truck_id) {
        try{
        $query = $this->db->get_where('truck', array('truck_id' => $truck_id));
        return $query->row_array();
        }catch(Exception $e){
            echo $e;
        }
    }

    public function create_truck($data) {
        try{
        if ($this->db->insert('truck', $data)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }catch(Exception $e){
        echo $e;
    }
    }

    public function update_truck($truck_id, $data) {
        try{
        $this->db->where('truck_id', $truck_id);
        return $this->db->update('truck', $data);
        }catch(Exception $e){
            echo $e;
        }
    }

    public function delete_truck($truck_id) {
        try{
        $this->db->where('truck_id', $truck_id);
        return $this->db->delete('truck');
        }catch(Exception $e){
            echo $e;
        }
    }

    public function check_duplicate($license_plate, $vin, $truck_id = null) {
        try{
        $this->db->where('license_plate', $license_plate);
        $this->db->or_where('vin', $vin);
        if ($truck_id) {
            $this->db->where('truck_id !=', $truck_id);
        }
        $query = $this->db->get('truck');
        return $query->num_rows() > 0;
    }catch(Exception $e){
        echo $e;
    }
    }
}
?>
