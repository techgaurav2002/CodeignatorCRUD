<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");

require_once APPPATH . 'helpers/jwt_helper.php';

class TruckController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Truck_model');
        $this->load->library( 'form_validation' );
    }

    private function verify_token() {
        $headers = $this->input->request_headers();
        if (!isset($headers['Authorization'])) {
            $this->output->set_status_header(401);
            echo json_encode(array('status' => false, 'message' => 'Unauthorized access'));
            exit();
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decoded_token = validateToken($token);
        
        if ($decoded_token === null) {
            $this->output->set_status_header(401);
            echo json_encode(array('status' => false, 'message' => 'Unauthorized access'));
            exit();
        }

        return $decoded_token;
    }

    public function index() {
        try{
        $this->verify_token();
        $trucks = $this->Truck_model->get_all_trucks();
        echo json_encode(array('status' => true, 'data' => $trucks));
        } catch(Exception $e){
            $this->output->set_status_header(500);
            echo json_encode(array('status' => false, 'message' => 'Internal server error','error' => $e));
        }
    }

    public function view($id) {
        try{
        $this->verify_token();
        $truck = $this->Truck_model->get_truck_by_id($id);
        if ($truck) {
            echo json_encode(array('status' => true, 'data' => $truck));
        } else {
            $this->output->set_status_header(404);
            echo json_encode(array('status' => false, 'message' => 'Truck not found'));
        }
    } catch(Exception $e){
        $this->output->set_status_header(500);
        echo json_encode(array('status' => false, 'message' => 'Internal server error','error' => $e));
    }
    }

    public function create() {
        try{
        $postData = json_decode(file_get_contents('php://input'), true);
        $admin_data = $this->verify_token();
        $this->form_validation->set_data($postData);
        $this->form_validation->set_rules('model', 'Model', 'required');
        $this->form_validation->set_rules('license_plate', 'License Plate', 'required|is_unique[truck.license_plate]');
        $this->form_validation->set_rules('vin', 'VIN', 'required|is_unique[truck.vin]');
        $this->form_validation->set_rules('manufacturer', 'Manufacturer', 'required');
        $this->form_validation->set_rules('year', 'Year', 'required|numeric');
        $this->form_validation->set_rules('driver_id', 'Driver ID', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            $this->output->set_status_header(400);
            echo json_encode(array('status' => false, 'message' => $errors));
        } else {
            
            $data = array(
                'model' => $postData['model'],
                'license_plate' => $postData['license_plate'],
                'vin' => $postData['vin'],
                'manufacturer' => $postData['manufacturer'],
                'year' => $postData['year'],
                'driver_id' => $postData['driver_id'],
                'created_by' => $admin_data['email'],
                'updated_by' => $admin_data['email']
            );

            if ($this->Truck_model->check_duplicate($data['license_plate'], $data['vin'])) {
                $this->output->set_status_header(400);
                echo json_encode(array('status' => false, 'message' => 'Duplicate license plate or VIN'));
            } else {
                $truck_id = $this->Truck_model->create_truck($data);
                if ($truck_id) {
                    echo json_encode(array('status' => true, 'message' => 'Truck created successfully', 'truck_id' => $truck_id));
                } else {
                    $this->output->set_status_header(500);
                    echo json_encode(array('status' => false, 'message' => 'Failed to create truck'));
                }
            }
        }
    } catch(Exception $e){
        $this->output->set_status_header(500);
        echo json_encode(array('status' => false, 'message' => 'Internal server error','error' => $e));
    }
    }

    public function update($id) {
        try{
        $postData = json_decode(file_get_contents('php://input'), true);
        $admin_data = $this->verify_token();
        $this->form_validation->set_data($postData);
        $this->form_validation->set_rules('model', 'Model', 'required');
        $this->form_validation->set_rules('license_plate', 'License Plate', 'required');
        $this->form_validation->set_rules('vin', 'VIN', 'required');
        $this->form_validation->set_rules('manufacturer', 'Manufacturer', 'required');
        $this->form_validation->set_rules('year', 'Year', 'required|numeric');
        $this->form_validation->set_rules('driver_id', 'Driver ID', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            $this->output->set_status_header(400);
            echo json_encode(array('status' => false, 'message' => $errors));
        } else {
           
            $data = array(
                'model' => $postData['model'],
                'license_plate' => $postData['license_plate'],
                'vin' => $postData['vin'],
                'manufacturer' => $postData['manufacturer'],
                'year' => $postData['year'],
                'driver_id' => $postData['driver_id'],
                'updated_by' => $admin_data['email']
            );
            $vin = $this->Truck_model->check_duplicate($data['license_plate'], $data['vin'], $id);
            // print_r($postData['vin']);
            print_r($vin);

            if ($vin == 0) {
                $this->output->set_status_header(400);
                echo json_encode(array('status' => false, 'message' => "Can't change the Licence no"));
            } else {
                if ($this->Truck_model->update_truck($id, $data)) {
                    echo json_encode(array('status' => true, 'message' => 'Truck updated successfully'));
                } else {
                    $this->output->set_status_header(500);
                    echo json_encode(array('status' => false, 'message' => 'Failed to update truck'));
                }
            }
        }
    } catch(Exception $e){
        $this->output->set_status_header(500);
        echo json_encode(array('status' => false, 'message' => 'Internal server error','error' => $e));
    }
    }

    public function delete($id) {
        try{
            $this->verify_token();
        if ($this->Truck_model->delete_truck($id)) {
            echo json_encode(array('status' => true, 'message' => 'Truck deleted successfully'));
        } else {
            $this->output->set_status_header(500);
            echo json_encode(array('status' => false, 'message' => 'Failed to delete truck'));
        }
    }catch(Exception $e){
        $this->output->set_status_header(500);
        echo json_encode(array('status' => false, 'message' => 'Internal server error','error' => $e));
    }
    }
}
?>
