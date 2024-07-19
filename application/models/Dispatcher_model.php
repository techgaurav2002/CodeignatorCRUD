<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dispatcher_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_dispatchers($id = NULL) {
        try{
        if ($id === NULL) {
            $query = $this->db->get('dispatcher');
            return $query->result_array();
        } else {
            $query = $this->db->get_where('dispatcher', array('dispatcher_id' => $id));
            return $query->row_array();
        }
    }catch(Exception $e){
        echo $e;
    }
    }

    public function create_dispatcher($data) {
        try{
        return $this->db->insert('dispatcher', $data);
        }catch(Exception $e){
            echo $e;
        }
    }

    public function update_dispatcher($id, $data) {
        try{
        $query = $this->db->get_where('dispatcher', array('dispatcher_id' => $id));
    //    print_r($query->row_array());
       if($query->row_array()){
        $this->db->where('dispatcher_id', $id);
        return $this->db->update('dispatcher',);
       }else{
        return null;
      }
    }catch(Exception $e){
        echo $e;
    }
    }

    public function delete_dispatcher($id) {
        try{
        $this->db->where('dispatcher_id', $id);
        return $this->db->delete('dispatcher');
        }catch(Exception $e){
            echo $e;
        }
    }
}
